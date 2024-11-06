<?php
session_start(); // Iniciar sesión para almacenar temporalmente los productos seleccionados
include 'conexion.php';

// Crear un nuevo número de orden basado en la fecha y hora actual si no existe
if (!isset($_SESSION['numero_de_orden'])) {
    $_SESSION['numero_de_orden'] = date('dmYHis'); // Formato ddmmaaaaHHMMSS
}

// Inicializar la lista de productos en la sesión
if (!isset($_SESSION['productos_orden'])) {
    $_SESSION['productos_orden'] = [];
}

// Manejar la inserción de productos en la orden
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_producto'])) {
    $id_inventario = $_POST['id_inventario'];
    $cantidad = $_POST['cantidad'];

    // Agregar el producto y la cantidad a la lista de productos de la orden
    $_SESSION['productos_orden'][] = [
        'id_inventario' => $id_inventario,
        'cantidad' => $cantidad
    ];
}

// Manejar la eliminación de un producto específico de la orden
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_producto'])) {
    $index = $_POST['index'];
    if (isset($_SESSION['productos_orden'][$index])) {
        unset($_SESSION['productos_orden'][$index]);
        $_SESSION['productos_orden'] = array_values($_SESSION['productos_orden']); // Reindexar el array
    }
}

// Manejar la finalización de la orden
if (isset($_POST['finalizar'])) {
    $numero_de_orden = $_SESSION['numero_de_orden'];
    foreach ($_SESSION['productos_orden'] as $producto) {
        $stmt = $db->prepare("INSERT INTO torden_de_compra (numero_de_orden, id_inventario, estado, cantidad) 
                              VALUES (:numero_de_orden, :id_inventario, 1, :cantidad)");
        $stmt->bindParam(':numero_de_orden', $numero_de_orden);
        $stmt->bindParam(':id_inventario', $producto['id_inventario']);
        $stmt->bindParam(':cantidad', $producto['cantidad']);
        $stmt->execute();
    }

    // Limpiar la lista de productos en la sesión
    unset($_SESSION['productos_orden']);
    unset($_SESSION['numero_de_orden']);
    echo "Orden de compra finalizada exitosamente.";
}

// Obtener los productos del inventario para mostrar en el formulario
$productosInventario = $db->query("SELECT 
                                      i.id_inventario, 
                                      p.nombre_producto, 
                                      m.nombre_marca, 
                                      pr.nombre_presentacion, 
                                      i.precio 
                                   FROM tinventario i 
                                   JOIN tproducto p ON i.id_producto = p.id_producto 
                                   JOIN tmarca m ON i.id_marca = m.id_marca
                                   JOIN tpresentacion pr ON i.id_presentacion = pr.id_presentacion")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        // Función para filtrar productos en tiempo real
        function filtrarProductos() {
            let input = document.getElementById("filtroProducto").value.toLowerCase();
            let filas = document.querySelectorAll("#tablaProductos tbody tr");

            filas.forEach(fila => {
                let texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(input) ? "" : "none";
            });
        }

        // Activar campo de cantidad al seleccionar un producto
        function seleccionarProducto(id, nombre) {
            document.getElementById("id_inventario").value = id;
            document.getElementById("productoSeleccionado").textContent = nombre;
            document.getElementById("cantidad").disabled = false;
            document.getElementById("cantidad").focus(); // Colocar el foco en el campo de cantidad
            document.getElementById("agregar_producto").disabled = false;
        }

        // Detectar Enter en el campo de cantidad para agregar el producto
        function submitOnEnter(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Evitar el envío del formulario completo
                document.getElementById("agregar_producto").click(); // Simular clic en el botón "Agregar Producto"
            }
        }
    </script>
</head>
<body>
<?php include "header.php"; ?>
<main>
<br><br><br><br>
    <h1>Número de Orden: <?= isset($_SESSION['numero_de_orden']) ? $_SESSION['numero_de_orden'] : 'N/A' ?></h1>

    <h2>Seleccionar Producto</h2>
    <label for="filtroProducto">Buscar producto:</label>
    <input type="text" id="filtroProducto" onkeyup="filtrarProductos()" placeholder="Filtrar productos...">

    <table border="1" id="tablaProductos">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Marca</th>
                <th>Presentación</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productosInventario as $producto): ?>
                <tr onclick="seleccionarProducto(<?= $producto['id_inventario'] ?>, '<?= $producto['nombre_producto'] ?>')">
                    <td><?= $producto['nombre_producto'] ?></td>
                    <td><?= $producto['nombre_marca'] ?></td>
                    <td><?= $producto['nombre_presentacion'] ?></td>
                    <td><?= number_format($producto['precio'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="" method="POST">
        <input type="hidden" name="id_inventario" id="id_inventario">
        
        <p>Producto seleccionado: <span id="productoSeleccionado">Ninguno</span></p>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" min="1" disabled required onkeypress="submitOnEnter(event)">
        <style>
            #agregar_producto, .finalizar {
    background-color: white;
    color: #4CAF50;
    border: 2px solid #28a745;
    padding: 10px;
}
#agregar_producto:hover, .finalizar {
    box-shadow: 0px 0px 15px #28a745;
}
        </style>

        <button type="submit" name="agregar_producto" id="agregar_producto" disabled>Agregar Producto</button>
    </form>
    <br>
    <hr>

    <h2>Productos Agregados a la Orden</h2>
    <table border="1">
        <tr>
            <th>Producto</th>
            <th>Marca</th>
            <th>Presentación</th>
            <th>Precio Unitario</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acción</th>
        </tr>
        <?php 
        $total = 0;
        if (isset($_SESSION['productos_orden']) && count($_SESSION['productos_orden']) > 0): ?>
            <?php foreach ($_SESSION['productos_orden'] as $index => $producto): 
                // Obtener detalles del producto
                $stmt = $db->prepare("SELECT 
                                          p.nombre_producto, 
                                          m.nombre_marca, 
                                          pr.nombre_presentacion, 
                                          i.precio 
                                      FROM tinventario i 
                                      JOIN tproducto p ON i.id_producto = p.id_producto 
                                      JOIN tmarca m ON i.id_marca = m.id_marca
                                      JOIN tpresentacion pr ON i.id_presentacion = pr.id_presentacion
                                      WHERE i.id_inventario = :id_inventario");
                $stmt->bindParam(':id_inventario', $producto['id_inventario']);
                $stmt->execute();
                $detalleProducto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $subtotal = $detalleProducto['precio'] * $producto['cantidad'];
                $total += $subtotal;
            ?>
                <tr> 
                    <td><?= $detalleProducto['nombre_producto'] ?></td>
                    <td><?= $detalleProducto['nombre_marca'] ?></td>
                    <td><?= $detalleProducto['nombre_presentacion'] ?></td>
                    <td><?= number_format($detalleProducto['precio'], 2) ?></td>
                    <td><?= $producto['cantidad'] ?></td>
                    <td><?= number_format($subtotal, 2) ?></td>
                    <td>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="index" value="<?= $index ?>">
                            <button type="submit" name="eliminar_producto">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                <td colspan="2"><strong><?= number_format($total, 2) ?></strong></td>
            </tr>
        <?php else: ?>
            <tr><td colspan="7">No se han agregado productos.</td></tr>
        <?php endif; ?>
    </table>

    <form action="" method="POST">
        <button class="finalizar" type="submit" name="finalizar">Finalizar Orden</button>
    </form>
</main>

<?php include "footer.php"; ?>
<script src="header.js"></script>
</body>
</html>
