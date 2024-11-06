<?php
// Archivo de conexión a SQLite
try {
    $db = new PDO('sqlite:restaurante.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
    exit();
}
?>
