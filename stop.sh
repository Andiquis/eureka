#!/bin/bash

# Variables
FILE_PATH="$PREFIX/etc/bash.bashrc"

# Definir el patrón de inicio y fin del bloque de código que queremos eliminar
START_PATTERN="Welcome to Eureka Terminal"
END_PATTERN="127.0.0.1:8002/index_database.php"

# Detener cualquier instancia de php en el puerto 8002
PHP_PID=$(lsof -t -i:8002)
if [ -n "$PHP_PID" ]; then
    kill "$PHP_PID"
    echo "Servidor PHP detenido en el puerto 8002."
else
    echo "No se encontró un servidor PHP ejecutándose en el puerto 8002."
fi

# Eliminar el bloque de código en bash.bashrc si existe
if grep -q "$START_PATTERN" "$FILE_PATH"; then
    sed -i "/$START_PATTERN/,/$END_PATTERN/d" "$FILE_PATH"
    echo "Las líneas han sido eliminadas de $FILE_PATH."
else
    echo "El bloque de código no se encontró en $FILE_PATH."
fi
