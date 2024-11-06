<?php
include 'conexion.php';

// Variables para mensajes
$mensaje = "";

// Manejar la inserción de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_producto'])) {
    $nombre_producto = trim($_POST['nombre_producto']);
    
    if (!empty($nombre_producto)) {
        // Verificar si el producto ya existe
        $stmt = $db->prepare("SELECT COUNT(*) FROM tproducto WHERE nombre_producto = :nombre_producto");
        $stmt->bindParam(':nombre_producto', $nombre_producto);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $mensaje = "El producto ya existe.";
        } else {
            // Inserción del nuevo producto
            $stmt = $db->prepare("INSERT INTO tproducto (nombre_producto) VALUES (:nombre_producto)");
            $stmt->bindParam(':nombre_producto', $nombre_producto);
            if ($stmt->execute()) {
                $mensaje = "Producto agregado exitosamente.";
            } else {
                $mensaje = "Error al agregar el producto.";
            }
        }
    } else {
        $mensaje = "El nombre del producto no puede estar vacío.";
    }
}

// Manejar la eliminación de un producto
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM tproducto WHERE id_producto = :delete_id");
    $stmt->bindParam(':delete_id', $delete_id);
    if ($stmt->execute()) {
        $mensaje = "Producto eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar el producto.";
    }
    header("Location: index_producto.php");
    exit();
}

// Manejar la edición de un producto
if (isset($_POST['edit_id']) && isset($_POST['edit_nombre_producto'])) {
    $edit_id = $_POST['edit_id'];
    $edit_nombre_producto = trim($_POST['edit_nombre_producto']);
    
    if (!empty($edit_nombre_producto)) {
        $stmt = $db->prepare("UPDATE tproducto SET nombre_producto = :nombre_producto WHERE id_producto = :id");
        $stmt->bindParam(':nombre_producto', $edit_nombre_producto);
        $stmt->bindParam(':id', $edit_id);
        if ($stmt->execute()) {
            $mensaje = "Producto editado exitosamente.";
        } else {
            $mensaje = "Error al editar el producto.";
        }
    } else {
        $mensaje = "El nombre del producto no puede estar vacío.";
    }
    header("Location: index_producto.php");
    exit();
}

// Obtener todos los productos para mostrar en la tabla
$productos = $db->query("SELECT * FROM tproducto")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restobar - Módulo de Productos</title>
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
            <h2>Módulo de Productos</h2>
            <form action="" method="POST">
                <label>Nombre del Producto:</label>
                <input type="text" name="nombre_producto" required>
                <button type="submit" class="button btn-add">Guardar</button>
            </form>
            <?php if (!empty($mensaje)): ?>
                <p class="mensaje"><?= $mensaje ?></p>
            <?php endif; ?>
        </section>

        <h2>Lista de Productos</h2>
        <table>
            <tr>
                <th>N°</th>
                <th>Productos</th>

            </tr>
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= $producto['id_producto'] ?></td>
                        
                        <td>
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="edit_id" value="<?= $producto['id_producto'] ?>">
                                <input type="text" name="edit_nombre_producto" value="<?= htmlspecialchars($producto['nombre_producto']) ?>" required>
                                <button type="submit" class="button btn-edit">Editar</button>
                            </form>
                            <a href="?delete_id=<?= $producto['id_producto'] ?>" class="button btn-delete">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay productos registrados.</td>
                </tr>
            <?php endif; ?>
        </table>
    </main>

    <?php include "footer.php"; ?>
    <script src="header.js"></script> <!-- Archivo JS para el header -->

</body>
</html>
