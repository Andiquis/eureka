#!/bin/bash

# Variables
FILE_PATH="$PREFIX/etc/bash.bashrc"

# Código a añadir al final del archivo con un mensaje de bienvenida decorado y validaciones
NEW_LINES=$(cat <<'EOF'
# Comprobación de paquetes php y sqlite3
if command -v php >/dev/null 2>&1 && command -v sqlite3 >/dev/null 2>&1; then
    echo -e "\033[1;32m========================================\033[0m"
    echo -e "\033[1;34m    Welcome to Eureka Terminal! 🚀\033[0m"
    echo -e "\033[1;32m========================================\033[0m"

    # Navegar al directorio y levantar el servidor
    cd $HOME/eureka || exit
    php -S 0.0.0.0:8002 &

    # Abrir la URL en Google Chrome
    am start -n com.android.chrome/com.google.android.apps.chrome.Main -d http://127.0.0.1:8002/index_database.php &
else
    echo -e "\033[1;31mError: Los paquetes php y sqlite3 no están instalados. Instálalos para continuar.\033[0m"
fi
EOF
)

# Comprobar si el código ya está añadido para evitar duplicados
if ! grep -q "Welcome to Eureka Terminal" "$FILE_PATH"; then
    echo "$NEW_LINES" >> "$FILE_PATH"
    echo "Líneas añadidas al final de $FILE_PATH"
else
    echo "El código ya existe en $FILE_PATH, no se realizaron cambios."
fi
