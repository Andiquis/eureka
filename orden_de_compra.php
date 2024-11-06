<?php
include 'conexion.php';

// Obtener todos los números de orden sin duplicados y ordenarlos de más reciente a más antiguo
$queryTodasOrdenes = "
    SELECT DISTINCT numero_de_orden 
    FROM torden_de_compra 
    ORDER BY id_orden DESC
";
$ordenesTodas = $db->query($queryTodasOrdenes)->fetchAll(PDO::FETCH_COLUMN);

// Obtener el número de orden seleccionado para filtrar, si existe
$numeroOrdenSeleccionado = $_POST['numero_de_orden'] ?? '';

// Construir la consulta de órdenes de compra con detalles del producto y filtro opcional
$query = "
    SELECT 
        o.id_orden,
        o.numero_de_orden,
        p.nombre_producto,
        m.nombre_marca,
        pr.nombre_presentacion,
        o.cantidad,
        i.precio,
        o.estado,
        (o.cantidad * i.precio) AS subtotal
    FROM torden_de_compra o
    JOIN tinventario i ON o.id_inventario = i.id_inventario
    JOIN tproducto p ON i.id_producto = p.id_producto
    JOIN tmarca m ON i.id_marca = m.id_marca
    JOIN tpresentacion pr ON i.id_presentacion = pr.id_presentacion
" . (!empty($numeroOrdenSeleccionado) ? " WHERE o.numero_de_orden = :numeroOrden" : "");

$stmt = $db->prepare($query);

if (!empty($numeroOrdenSeleccionado)) {
    $stmt->bindParam(':numeroOrden', $numeroOrdenSeleccionado);
}

$stmt->execute();
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejo de actualización de estado por AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado'], $_POST['id_orden'])) {
    $queryUpdate = "UPDATE torden_de_compra SET estado = :estado WHERE id_orden = :id_orden";
    $stmtUpdate = $db->prepare($queryUpdate);
    $stmtUpdate->execute([':estado' => $_POST['estado'], ':id_orden' => $_POST['id_orden']]);
    echo json_encode(['success' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Órdenes de Compra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        h1 { text-align: center; color: #333; }
        .table-container { max-width: 100%; overflow-x: auto; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        
        /* Estilo de la tabla y bordes */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; } /* Borde en celdas */
        th { background-color: #007BFF; color: white; border: 1px solid #ddd; } /* Borde en encabezado */

        /* Colores de estado */
        tr.estado-0 td { background-color: #4CAF50; color: white; } /* Comprado */
        tr.estado-1 td { background-color: #FF9800; color: white; } /* Falta */
        tr.estado-2 td { background-color: #F44336; color: white; } /* Sin Stock */
        
        select { padding: 5px; border-radius: 5px; border: 1px solid #ccc; width: 100%; }
        
        /* Responsivo */
        @media (max-width: 768px) {
            body { padding: 10px; }
            th, td { padding: 8px; font-size: 14px; }
            h1 { font-size: 1.5em; }
        }
    </style>
    <script>
        function actualizarEstado(idOrden) {
            var nuevoEstado = document.getElementById('estado_' + idOrden).value;
            var fila = document.getElementById('fila_' + idOrden);
            
            // Quitar clases previas de estado para evitar conflictos
            fila.classList.remove('estado-0', 'estado-1', 'estado-2');
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Aplicar nueva clase de estado
                        fila.classList.add('estado-' + nuevoEstado);
                    } else {
                        console.error('Error al actualizar el estado');
                    }
                }
            };
            xhr.send("estado=" + nuevoEstado + "&id_orden=" + idOrden);
        }
    </script>
</head>
<body>
<?php
    include "header.php";
    ?>
    <h1>Órdenes de Compra</h1>
    
    <form method="POST">
        <label for="numero_de_orden">Filtrar por Número de Orden:</label>
        <select name="numero_de_orden" id="numero_de_orden" onchange="this.form.submit()">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($ordenesTodas as $numeroOrden): ?>
                <option value="<?= $numeroOrden ?>" <?= $numeroOrden == $numeroOrdenSeleccionado ? 'selected' : '' ?>><?= $numeroOrden ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    
    <div class="table-container">
        <table>
            <tr>
                <th>Nombre del Producto</th>
                <th>Marca</th>
                <th>Presentación</th>
                <th>Cantidad</th>
                <th>Estado</th>
            </tr>
            <?php if (count($ordenes) > 0): ?>
                <?php foreach ($ordenes as $orden): ?>
                    <?php $estadoClase = 'estado-' . $orden['estado']; ?>
                    <tr id="fila_<?= $orden['id_orden'] ?>" class="<?= $estadoClase ?>">
                        <td><?= $orden['nombre_producto'] ?></td>
                        <td><?= $orden['nombre_marca'] ?></td>
                        <td><?= $orden['nombre_presentacion'] ?></td>
                        <td><?= $orden['cantidad'] ?></td>
                        <td>
                            <select id="estado_<?= $orden['id_orden'] ?>" onchange="actualizarEstado(<?= $orden['id_orden'] ?>)">
                                <option value="1" <?= $orden['estado'] == 1 ? 'selected' : '' ?>>Falta</option>
                                <option value="0" <?= $orden['estado'] == 0 ? 'selected' : '' ?>>Comprado</option>
                                <option value="2" <?= $orden['estado'] == 2 ? 'selected' : '' ?>>Sin Stock</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay órdenes de compra registradas.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php
    include "footer.php";
    ?>
    <script src="header.js"></script>
</body>
</html>
