#!/bin/bash

# Variables
FILE_PATH="$PREFIX/etc/bash.bashrc"

# Código a añadir al final del archivo con un mensaje de bienvenida decorado, validaciones y creación de base de datos
NEW_LINES=$(cat <<'EOF'
# Comprobación de paquetes php y sqlite3
if command -v php >/dev/null 2>&1 && command -v sqlite3 >/dev/null 2>&1; then
    echo -e "\033[1;32m========================================\033[0m"
    echo -e "\033[1;34m    Welcome to Eureka Terminal! 🚀\033[0m"
    echo -e "\033[1;32m========================================\033[0m"

    # Verificar si la base de datos restaurante.db existe, si no, crearla ejecutando db_create.php
    DB_PATH="$HOME/eureka/restaurante.db"
    if [ ! -f "$DB_PATH" ]; then
        echo "Base de datos restaurante.db no encontrada. Creándola..."
        php "$HOME/eureka/db_create.php"
        echo "Base de datos restaurante.db creada."
    else
        echo "Base de datos restaurante.db ya existe."
    fi

    # Otorgar permisos de ejecución a todos los archivos dentro de eureka
    chmod +x $HOME/eureka/* 
    echo "Permisos de ejecución otorgados a los archivos en el directorio eureka."

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
