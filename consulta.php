<?php
include 'conexion.php'; // Asegúrate de incluir tu archivo de conexión

// Consulta para obtener todos los números de orden sin repetir, ordenados de más reciente a más antiguo
$queryTodasOrdenes = "
    SELECT DISTINCT numero_de_orden 
    FROM torden_de_compra 
    ORDER BY id_orden DESC
";
$ordenesTodas = $db->query($queryTodasOrdenes)->fetchAll(PDO::FETCH_COLUMN);

// Inicializar variable para el número de orden filtrado
$numeroOrdenSeleccionado = isset($_POST['numero_de_orden']) ? $_POST['numero_de_orden'] : '';

// Consulta para obtener las órdenes de compra junto con los detalles del producto
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
";

// Añadir filtro por número de orden si se ha seleccionado uno
if (!empty($numeroOrdenSeleccionado)) {
    $query .= " WHERE o.numero_de_orden = :numeroOrden";
}

$stmt = $db->prepare($query);

// Si se ha seleccionado un número de orden, enlazamos el parámetro
if (!empty($numeroOrdenSeleccionado)) {
    $stmt->bindParam(':numeroOrden', $numeroOrdenSeleccionado);
}

// Ejecutar la consulta
$stmt->execute();
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular el total de precios
$total = 0;
foreach ($ordenes as $orden) {
    $total += $orden['subtotal'];
}

// Manejo de la actualización del estado del producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado'], $_POST['id_orden'])) {
    $nuevoEstado = $_POST['estado'];
    $idOrden = $_POST['id_orden'];

    $queryUpdate = "UPDATE torden_de_compra SET estado = :estado WHERE id_orden = :id_orden";
    $stmtUpdate = $db->prepare($queryUpdate);
    $stmtUpdate->bindParam(':estado', $nuevoEstado);
    $stmtUpdate->bindParam(':id_orden', $idOrden);
    $stmtUpdate->execute();

    // Opción de respuesta para AJAX
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        form {
            margin: 20px 0;
            text-align: center;
        }

        select {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Colores suaves según el estado */
        .estado-comprado {
            background-color: #d4edda; /* Verde claro */
        }

        .estado-falta {
            background-color: #fff3cd; /* Amarillo claro */
        }

        .estado-sin-stock {
            background-color: #f8d7da; /* Rojo claro */
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            table, th, td {
                font-size: 14px;
                padding: 8px;
            }

            h1 {
                font-size: 1.5em;
            }
        }
    </style>
    <script>
        function actualizarEstado(idOrden) {
            var selectEstado = document.getElementById('estado_' + idOrden);
            var nuevoEstado = selectEstado.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            console.log('Estado actualizado con éxito');
                        } else {
                            console.error('Error al actualizar el estado');
                        }
                    }
                }
            };
            xhr.send("estado=" + nuevoEstado + "&id_orden=" + idOrden);
        }
    </script>
</head>
<body>
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
    
    <table>
        <tr>
            <th>ID Orden</th>
            <th>Número de Orden</th>
            <th>Nombre del Producto</th>
            <th>Marca</th>
            <th>Presentación</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
            <th>Estado</th>
        </tr>
        <?php if (count($ordenes) > 0): ?>
            <?php foreach ($ordenes as $orden): ?>
                <tr class="<?php 
                    if ($orden['estado'] == 0) {
                        echo 'estado-comprado';
                    } elseif ($orden['estado'] == 1) {
                        echo 'estado-falta';
                    } elseif ($orden['estado'] == 2) {
                        echo 'estado-sin-stock';
                    }
                ?>">
                    <td><?= $orden['id_orden'] ?></td>
                    <td><?= $orden['numero_de_orden'] ?></td>
                    <td><?= $orden['nombre_producto'] ?></td>
                    <td><?= $orden['nombre_marca'] ?></td>
                    <td><?= $orden['nombre_presentacion'] ?></td>
                    <td><?= $orden['cantidad'] ?></td>
                    <td><?= number_format($orden['precio'], 2) ?></td>
                    <td><?= number_format($orden['subtotal'], 2) ?></td>
                    <td>
                        <select id="estado_<?= $orden['id_orden'] ?>" onchange="actualizarEstado(<?= $orden['id_orden'] ?>)">
                            <option value="0" <?= $orden['estado'] == 0 ? 'selected' : '' ?>>Comprado</option>
                            <option value="1" <?= $orden['estado'] == 1 ? 'selected' : '' ?>>Falta</option>
                            <option value="2" <?= $orden['estado'] == 2 ? 'selected' : '' ?>>Sin Stock</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="7" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong><?= number_format($total, 2) ?></strong></td>
                <td></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="9">No hay órdenes de compra registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
