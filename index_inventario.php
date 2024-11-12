<?php
include 'conexion.php';

// Variables para mensajes
$mensaje = "";

// Obtener opciones para las listas desplegables
$productos = $db->query("SELECT id_producto, nombre_producto FROM tproducto")->fetchAll(PDO::FETCH_ASSOC);
$marcas = $db->query("SELECT id_marca, nombre_marca FROM tmarca")->fetchAll(PDO::FETCH_ASSOC);
$presentaciones = $db->query("SELECT id_presentacion, nombre_presentacion FROM tpresentacion")->fetchAll(PDO::FETCH_ASSOC);

// Manejar la inserción de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id_producto = $_POST['id_producto'];
    $id_marca = $_POST['id_marca'];
    $id_presentacion = $_POST['id_presentacion'];
    $precio = $_POST['precio'];
    $fecha_registro = $_POST['fecha_registro'];
    $detalles = !empty($_POST['detalles']) ? $_POST['detalles'] : 'ninguna';

    // Verificar si ya existe el inventario
    $stmt = $db->prepare("SELECT COUNT(*) FROM tinventario WHERE id_producto = :id_producto AND id_marca = :id_marca AND id_presentacion = :id_presentacion");
    $stmt->bindParam(':id_producto', $id_producto);
    $stmt->bindParam(':id_marca', $id_marca);
    $stmt->bindParam(':id_presentacion', $id_presentacion);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        $mensaje = "Error: Este inventario ya existe.";
    } else {
        // Preparar la consulta de inserción
        $stmt = $db->prepare("INSERT INTO tinventario (id_producto, id_marca, id_presentacion, precio, fecha_registro, detalles) 
                              VALUES (:id_producto, :id_marca, :id_presentacion, :precio, :fecha_registro, :detalles)");
        
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->bindParam(':id_marca', $id_marca);
        $stmt->bindParam(':id_presentacion', $id_presentacion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':fecha_registro', $fecha_registro);
        $stmt->bindParam(':detalles', $detalles);
        
        // Ejecutar la inserción y establecer mensaje
        if ($stmt->execute()) {
            $mensaje = "Inventario agregado exitosamente.";
        } else {
            $mensaje = "Error al agregar el inventario.";
        }
    }
}

// Manejar la eliminación de inventarios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_inventario = $_POST['id_inventario'];

    // Preparar la consulta de eliminación
    $stmt = $db->prepare("DELETE FROM tinventario WHERE id_inventario = :id_inventario");
    $stmt->bindParam(':id_inventario', $id_inventario);
    
    // Ejecutar la eliminación y establecer mensaje
    if ($stmt->execute()) {
        $mensaje = "Inventario eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar el inventario.";
    }
}

// Obtener todos los inventarios con los nombres en lugar de los IDs
$inventarios = $db->query("SELECT 
        tinventario.id_inventario,
        tproducto.nombre_producto,
        tmarca.nombre_marca,
        tpresentacion.nombre_presentacion,
        tinventario.precio,
        tinventario.fecha_registro,
        tinventario.detalles
    FROM tinventario
    JOIN tproducto ON tinventario.id_producto = tproducto.id_producto
    JOIN tmarca ON tinventario.id_marca = tmarca.id_marca
    JOIN tpresentacion ON tinventario.id_presentacion = tpresentacion.id_presentacion")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Inventario</title>
    <link rel="stylesheet" href="style.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir JavaScript de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
$(document).ready(function() {
    // Inicializamos Select2 en todos los selects
    $('select').select2();

    // Detectamos cuando cualquier select se abre
    $('select').on('select2:open', function() {
        // Esperamos un momento para asegurarnos que el dropdown se haya abierto
        setTimeout(function() {
            // Establecer el foco en el campo de búsqueda, con una prioridad alta
            var searchField = $('.select2-search__field');
            if (searchField.length) {
                // Asegurarnos de que el input esté disponible y lo enfocamos
                searchField[0].focus();
                searchField[0].setSelectionRange(0, searchField.val().length); // Selecciona todo el texto si ya hay algo
            }
        }, 100); // 100ms de espera para asegurar que el dropdown haya sido completamente abierto
    });
});
    </script>

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
        input[type="text"], input[type="date"], input[type="number"] { padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .mensaje { color: green; margin-top: 1rem; }
        .error { color: red; }

        /* Estilos para Select2 */
        .select2-container {
            width: 100% !important;
        }

        /* Estilo para el texto dentro del desplegable */
        .select2-selection__rendered {
            color: black !important; /* Color negro para el texto seleccionado */
        }

        /* Estilo para las opciones dentro del desplegable */
        .select2-results__option {
            color: black !important; /* Color negro para las opciones */
        }

        /* Cambio del cursor al pasar sobre el select */
        .select2-selection--single {
            cursor: pointer !important; /* Cursor de "manita" para indicar interactividad */
        }

        /* Efecto de enfoque en el desplegable */
        .select2-selection__rendered:focus {
            outline: none; /* Eliminar borde por defecto en enfoque */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Agregar sombra sutil */
        }

        /* Ajustes de tamaño y estilo */
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px;
        }
    </style>

</head>
<body>
<?php include "header.php"; ?>

<main class="container">
    <section id="agregar-inventario" class="section">
        <h2>Módulo de Inventario</h2>
        <!-- Formulario para agregar inventario -->
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <label>Producto:</label>
            <select name="id_producto" id="producto" required>
                <?php foreach ($productos as $producto) : ?>
                    <option value="<?= $producto['id_producto'] ?>"><?= $producto['nombre_producto'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Marca:</label>
            <select name="id_marca" id="marca" required>
                <?php foreach ($marcas as $marca) : ?>
                    <option value="<?= $marca['id_marca'] ?>"><?= $marca['nombre_marca'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Presentación:</label>
            <select name="id_presentacion" id="presentacion" required>
                <?php foreach ($presentaciones as $presentacion) : ?>
                    <option value="<?= $presentacion['id_presentacion'] ?>"><?= $presentacion['nombre_presentacion'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Precio:</label>
            <input type="number" name="precio" step="0.01" required>

            <label>Fecha de Registro:</label>
            <input type="date" name="fecha_registro" required>

            <label>Detalles:</label>
            <input type="text" name="detalles">

            <button type="submit" class="button btn-add">Agregar Inventario</button>
        </form>
        <div class="mensaje"><?= $mensaje ?></div>

        <!-- Tabla de Inventarios -->
        <h3>Inventarios Registrados</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>Presentación</th>
                <th>Precio</th>
                <th>Fecha de Registro</th>
                <th>Detalles</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inventarios as $inventario) : ?>
                <tr>
                    <td><?= $inventario['id_inventario'] ?></td>
                    <td><?= $inventario['nombre_producto'] ?></td>
                    <td><?= $inventario['nombre_marca'] ?></td>
                    <td><?= $inventario['nombre_presentacion'] ?></td>
                    <td><?= $inventario['precio'] ?></td>
                    <td><?= $inventario['fecha_registro'] ?></td>
                    <td><?= $inventario['detalles'] ?></td>
                    <td>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_inventario" value="<?= $inventario['id_inventario'] ?>">
                            <button type="submit" class="button btn-delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>
