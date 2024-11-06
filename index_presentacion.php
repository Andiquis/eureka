<?php
include 'conexion.php';

// Variables para mensajes
$mensaje = "";

// Manejar la inserción de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_presentacion'])) {
    $nombre_presentacion = trim($_POST['nombre_presentacion']);
    
    if (!empty($nombre_presentacion)) {
        // Verificar si la presentación ya existe
        $stmt = $db->prepare("SELECT COUNT(*) FROM tpresentacion WHERE nombre_presentacion = :nombre_presentacion");
        $stmt->bindParam(':nombre_presentacion', $nombre_presentacion);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $mensaje = "La presentación ya existe.";
        } else {
            // Inserción de la nueva presentación
            $stmt = $db->prepare("INSERT INTO tpresentacion (nombre_presentacion) VALUES (:nombre_presentacion)");
            $stmt->bindParam(':nombre_presentacion', $nombre_presentacion);
            if ($stmt->execute()) {
                $mensaje = "Presentación agregada exitosamente.";
            } else {
                $mensaje = "Error al agregar la presentación.";
            }
        }
    } else {
        $mensaje = "El nombre de la presentación no puede estar vacío.";
    }
}

// Manejar la eliminación de una presentación
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM tpresentacion WHERE id_presentacion = :delete_id");
    $stmt->bindParam(':delete_id', $delete_id);
    if ($stmt->execute()) {
        $mensaje = "Presentación eliminada exitosamente.";
    } else {
        $mensaje = "Error al eliminar la presentación.";
    }
    header("Location: index_presentacion.php");
    exit();
}

// Manejar la edición de una presentación
if (isset($_POST['edit_id']) && isset($_POST['edit_nombre_presentacion'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nombre_presentacion = trim($_POST['edit_nombre_presentacion']);
    
    if (!empty($edit_nombre_presentacion)) {
        $stmt = $db->prepare("UPDATE tpresentacion SET nombre_presentacion = :nombre_presentacion WHERE id_presentacion = :id");
        $stmt->bindParam(':nombre_presentacion', $edit_nombre_presentacion);
        $stmt->bindParam(':id', $edit_id);
        if ($stmt->execute()) {
            $mensaje = "Presentación editada exitosamente.";
        } else {
            $mensaje = "Error al editar la presentación.";
        }
    } else {
        $mensaje = "El nombre de la presentación no puede estar vacío.";
    }
    header("Location: index_presentacion.php");
    exit();
}

// Obtener todas las presentaciones para mostrar en la tabla
$presentaciones = $db->query("SELECT * FROM tpresentacion")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restobar - Módulo de Presentaciones</title>
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
<?php include "header.php"; ?>

<main class="container">
    <section id="database" class="section">
        <h2>Módulo de Presentaciones</h2>
        <form action="" method="POST">
            <label>Nombre de la Presentación:</label>
            <input type="text" name="nombre_presentacion" required>
            <button type="submit" class="button btn-add">Guardar</button>
        </form>
        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?= $mensaje ?></p>
        <?php endif; ?>
    </section>

    <h2>Lista de Presentaciones</h2>
    <table>
        <tr>
            <th>N°</th>
            <th>Presentaciones</th>
        </tr>
        <?php if (!empty($presentaciones)): ?>
            <?php foreach ($presentaciones as $presentacion): ?>
                <tr>
                    <td><?= $presentacion['id_presentacion'] ?></td>
                   
                    <td>
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="edit_id" value="<?= $presentacion['id_presentacion'] ?>">
                            <input type="text" name="edit_nombre_presentacion" value="<?= htmlspecialchars($presentacion['nombre_presentacion']) ?>" required>
                            <button type="submit" class="button btn-edit">Editar</button>
                        </form>
                        <a href="?delete_id=<?= $presentacion['id_presentacion'] ?>" class="button btn-delete">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No hay presentaciones registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
</main>

<?php include "footer.php"; ?>
<script src="header.js"></script> <!-- Archivo JS para el header -->

</body>
</html>
