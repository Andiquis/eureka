<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recetas Deliciosas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos generales */


        /* Encabezado */


        /* Contenedor de recetas */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 2rem;
          
        }

        .recipe-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .recipe-card:hover {
            transform: translateY(-10px);
        }

        .recipe-title {
            font-size: 1.5rem;
            color: #ff6347;
            margin: 0 0 1rem 0;
        }

        .ingredients, .instructions {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }

        .ingredients h3, .instructions h3 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        /* Estilos de enlace */
        a {
            color: #ff6347;
            text-decoration: none;
            font-weight: bold;
        }

        /* Pie de página */
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>

    <?php
    include "header.php";
    ?>
<br><br><br>
    <div class="container">
        <!-- Salsa Crispi -->
        <div class="recipe-card">
            <h2 class="recipe-title">Salsa Crispi</h2>
            <div class="ingredients">
                <h3>Ingredientes:</h3>
                <ul>
                    <li>2 cdas de mayonesa</li>
                    <li>1 cda de mostaza</li>
                    <li>1 cda de miel</li>
                    <li>1 cda de ajo en polvo</li>
                </ul>
            </div>
            <div class="instructions">
                <h3>Instrucciones:</h3>
                <p>Mezcla todos los ingredientes en un bol hasta obtener una consistencia suave y cremosa. Sirve como aderezo o acompañamiento.</p>
            </div>
        </div>

        <!-- Salsa Barbecue -->
        <div class="recipe-card">
            <h2 class="recipe-title">Salsa Barbecue</h2>
            <div class="ingredients">
                <h3>Ingredientes:</h3>
                <ul>
                    <li>1 taza de ketchup</li>
                    <li>1/4 taza de vinagre de manzana</li>
                    <li>1/4 taza de miel</li>
                    <li>1 cdta de ajo en polvo</li>
                </ul>
            </div>
            <div class="instructions">
                <h3>Instrucciones:</h3>
                <p>Combina todos los ingredientes en una sartén y cocina a fuego lento durante 15 minutos. Deja enfriar y utiliza para marinar o acompañar carnes.</p>
            </div>
        </div>

        <!-- Mayonesa Casera -->
        <div class="recipe-card">
            <h2 class="recipe-title">Mayonesa Casera</h2>
            <div class="ingredients">
                <h3>Ingredientes:</h3>
                <ul>
                    <li>1 huevo</li>
                    <li>1 taza de aceite</li>
                    <li>1 cdta de mostaza</li>
                    <li>Sal y limón al gusto</li>
                </ul>
            </div>
            <div class="instructions">
                <h3>Instrucciones:</h3>
                <p>Bate el huevo y añade el aceite poco a poco hasta obtener una mezcla espesa. Agrega mostaza, sal y limón al gusto.</p>
            </div>
        </div>

        <!-- Ají Picante -->
        <div class="recipe-card">
            <h2 class="recipe-title">Ají Picante</h2>
            <div class="ingredients">
                <h3>Ingredientes:</h3>
                <ul>
                    <li>2 ajíes amarillos</li>
                    <li>1 diente de ajo</li>
                    <li>Sal al gusto</li>
                </ul>
            </div>
            <div class="instructions">
                <h3>Instrucciones:</h3>
                <p>Licua los ajíes junto con el ajo y la sal hasta obtener una salsa espesa. Ideal para acompañar comidas criollas.</p>
            </div>
        </div>

        <!-- Té Regular -->
        <div class="recipe-card">
            <h2 class="recipe-title">Preparación de Té</h2>
            <div class="ingredients">
                <h3>Ingredientes:</h3>
                <ul>
                    <li>1 bolsita de té</li>
                    <li>Agua caliente</li>
                    <li>Miel o azúcar al gusto</li>
                </ul>
            </div>
            <div class="instructions">
                <h3>Instrucciones:</h3>
                <p>Coloca la bolsita en agua caliente y deja reposar 3-5 minutos. Agrega miel o azúcar si deseas endulzarlo.</p>
            </div>
        </div>

        <!-- Salchipapas -->
        <div class="recipe-card">
            <h2 class="recipe-title">Salchipapas</h2>
            <div class="ingredients">
                <h3>Ingredientes:</h3>
                <ul>
                    <li>2 salchichas</li>
                    <li>2 papas grandes</li>
                    <li>Sal al gusto</li>
                    <li>Aceite para freír</li>
                </ul>
            </div>
            <div class="instructions">
                <h3>Instrucciones:</h3>
                <p>Corta las papas y las salchichas en tiras. Fríe las papas hasta que estén doradas y añade las salchichas. Sirve con salsas.</p>
            </div>
        </div>
        
        <!-- Otras recetas pueden seguir el mismo formato... -->

    </div>

    <?php include "footer.php"; ?>
    <script src="header.js"></script> <!-- Archivo JS para el header -->

</body>
</html>
