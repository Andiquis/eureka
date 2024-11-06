<?php
// Crear o conectar a la base de datos SQLite
$db = new PDO('sqlite:restaurante.db');

// Habilitar el modo de errores para capturar cualquier problema
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



try {
    // Crear tabla tproducto
    $db->exec("CREATE TABLE IF NOT EXISTS tproducto (
        id_producto INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre_producto TEXT NOT NULL
    )");

    // Crear tabla tmarca
    $db->exec("CREATE TABLE IF NOT EXISTS tmarca (
        id_marca INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre_marca TEXT NOT NULL
    )");

    // Crear tabla tpresentacion
    $db->exec("CREATE TABLE IF NOT EXISTS tpresentacion (
        id_presentacion INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre_presentacion TEXT NOT NULL
    )");

    // Crear tabla tinventario
    $db->exec("CREATE TABLE IF NOT EXISTS tinventario (
        id_inventario INTEGER PRIMARY KEY AUTOINCREMENT,
        id_producto INTEGER,
        id_marca INTEGER,
        id_presentacion INTEGER,
        precio REAL NOT NULL,
        fecha_registro TEXT,
        detalles TEXT DEFAULT 'ninguna',
        FOREIGN KEY (id_producto) REFERENCES tproducto(id_producto),
        FOREIGN KEY (id_marca) REFERENCES tmarca(id_marca),
        FOREIGN KEY (id_presentacion) REFERENCES tpresentacion(id_presentacion)
    )");
    
    // Crear tabla torden_de_compra
 // Crear tabla torden_de_compra
    $db->exec("CREATE TABLE IF NOT EXISTS torden_de_compra (
        id_orden INTEGER PRIMARY KEY AUTOINCREMENT,
        numero_de_orden INTEGER NOT NULL,          
        id_inventario INTEGER,
        estado INTEGER NOT NULL,
        cantidad INTEGER NOT NULL DEFAULT 1,
        FOREIGN KEY (id_inventario) REFERENCES tinventario(id_inventario)
    )");


    echo "Base de datos y tablas creadas con éxito.";

} catch (PDOException $e) {
    echo "Error al crear la base de datos: " . $e->getMessage();
}

// Cerrar conexión
$db = null;
?>
