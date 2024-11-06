<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restobar - Página Principal</title>
    <!-- Enlace a Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    

</head>
<body>
    <?php
    include "header.php";
    ?>

    <main>
        <section id="database" class="section">
            <h2>Base de Datos</h2>

            <div class="menu-options">
                <ul>
                    <li>
                        <a href="index_marca.php">
                            <i class="fas fa-tags"></i>
                            Marcas
                        </a>
                    </li>
                    <li>
                        <a href="index_producto.php">
                            <i class="fas fa-box-open"></i>
                            Productos
                        </a>
                    </li>
                    <li>
                        <a href="index_presentacion.php">
                            <i class="fas fa-cube"></i>
                            Presentaciones
                        </a>
                    </li>
                    <li>
                        <a href="index_inventario.php">
                            <i class="fas fa-warehouse"></i>
                            Inventario
                        </a>
                    </li>
                </ul>
            </div>
        </section>
    </main>

    <?php
    include "footer.php";
    ?>

<script src="header.js"></script>
</body>
</html>
