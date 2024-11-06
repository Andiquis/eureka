<?php
include 'conexion.php';

// Variables para mensajes
$mensaje = "";

// Manejar la inserción de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_marca'])) {
    $nombre_marca = trim($_POST['nombre_marca']);
    
    if (!empty($nombre_marca)) {
        // Verificar si la marca ya existe
        $stmt = $db->prepare("SELECT COUNT(*) FROM tmarca WHERE nombre_marca = :nombre_marca");
        $stmt->bindParam(':nombre_marca', $nombre_marca);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $mensaje = "La marca ya existe.";
        } else {
            // Inserción de la nueva marca
            $stmt = $db->prepare("INSERT INTO tmarca (nombre_marca) VALUES (:nombre_marca)");
            $stmt->bindParam(':nombre_marca', $nombre_marca);
            if ($stmt->execute()) {
                $mensaje = "Marca agregada exitosamente.";
            } else {
                $mensaje = "Error al agregar la marca.";
            }
        }
    } else {
        $mensaje = "El nombre de la marca no puede estar vacío.";
    }
}

// Manejar la eliminación de una marca
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM tmarca WHERE id_marca = :delete_id");
    $stmt->bindParam(':delete_id', $delete_id);
    if ($stmt->execute()) {
        $mensaje = "Marca eliminada exitosamente.";
    } else {
        $mensaje = "Error al eliminar la marca.";
    }
    header("Location: index_marca.php");
    exit();
}

// Manejar la edición de una marca
if (isset($_POST['edit_id']) && isset($_POST['edit_nombre_marca'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nombre_marca = trim($_POST['edit_nombre_marca']);
    
    if (!empty($edit_nombre_marca)) {
        $stmt = $db->prepare("UPDATE tmarca SET nombre_marca = :nombre_marca WHERE id_marca = :id");
        $stmt->bindParam(':nombre_marca', $edit_nombre_marca);
        $stmt->bindParam(':id', $edit_id);
        if ($stmt->execute()) {
            $mensaje = "Marca editada exitosamente.";
        } else {
            $mensaje = "Error al editar la marca.";
        }
    } else {
        $mensaje = "El nombre de la marca no puede estar vacío.";
    }
    header("Location: index_marca.php");
    exit();
}

// Obtener todas las marcas para mostrar en la tabla
$marcas = $db->query("SELECT * FROM tmarca")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restobar - Modulo de Marcas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos básicos */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 1rem; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: center; border: 1px solid #ddd; }
        th { background-color: #333; color: #fff; }
        td { background-color: #f9f9f9; }

        /* Botones */
        .button { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; }
        .btn-add { background-color: #4CAF50; color: white; }
        .btn-edit { background-color: #ffc107; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .button:hover { opacity: 0.8; }

        /* Formulario */
        form { display: flex; flex-direction: column; gap: 0.75rem; max-width: 300px; margin-top: 1rem; }
        input[type="text"] { padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .mensaje { color: green; margin-top: 1rem; }
        .error { color: red; }
    </style>
</head>
<body>
<?php
    include "header.php";
    ?>

    <main class="container">
        <section id="database" class="section">
            <h2>Modulo de Marcas</h2>
            <form action="" method="POST">
                <label>Nombre de la Marca:</label>
                <input type="text" name="nombre_marca" required>
                <button type="submit" class="button btn-add">Guardar</button>
            </form>
            <?php if (!empty($mensaje)): ?>
                <p class="mensaje"><?= $mensaje ?></p>
            <?php endif; ?>
        </section>

        <h2>Lista de Marcas</h2>
        <table>
            <tr>
                <th>N°</th>
                <th>Marcas</th>
            </tr>
            <?php 
            if (!empty($marcas)): // Verifica si hay marcas
                foreach ($marcas as $index => $marca): ?>
                    <tr>
                        <td style="color:black;"><?= $index + 1 ?></td>
                        <td>
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="edit_id" value="<?= $marca['id_marca'] ?>">
                                <input type="text" name="edit_nombre_marca" value="<?= htmlspecialchars($marca['nombre_marca']) ?>">
                                <button type="submit" class="button btn-edit">Editar</button>
                            </form>
                            <a href="?delete_id=<?= $marca['id_marca'] ?>" class="button btn-delete">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="2">No hay marcas registradas.</td>
                </tr>
            <?php endif; ?>
        </table>
    </main>

    <footer>
        <p>&copy; 2023 Restobar. Todos los derechos reservados.</p>
    </footer>

    <script>
        document.getElementById('nav-toggle').addEventListener('click', function () {
            var navbar = document.getElementById('navbar').querySelector('ul');
            navbar.classList.toggle('active');
        });
    </script>
</body>
</html>
