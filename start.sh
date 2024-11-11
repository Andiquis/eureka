#!/bin/bash

# Variables
FILE_PATH="$PREFIX/etc/bash.bashrc"

# Código a añadir al final del archivo
NEW_LINES="
cd \$HOME/eureka
php -S 0.0.0.0:8002 &
am start -n com.android.chrome/com.google.android.apps.chrome.Main -d http://127.0.0.1:8002/index_database.php &
"

# Comprobar si el código ya está añadido para evitar duplicados
if ! grep -q "cd \$HOME/eureka" "$FILE_PATH"; then
    echo "$NEW_LINES" >> "$FILE_PATH"
    echo "Líneas añadidas al final de $FILE_PATH"
else
    echo "El código ya existe en $FILE_PATH, no se realizaron cambios."
fi
