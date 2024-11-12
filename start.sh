#!/bin/bash

# Variables
FILE_PATH="$PREFIX/etc/bash.bashrc"

# Crear contenido para la nueva interfaz en bash.bashrc
NEW_LINES=$(cat <<'EOF'
# Configuración inicial para la interfaz de bienvenida y configuraciones
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

    # Dar permisos de ejecución a todos los archivos en el directorio eureka
    chmod +x $HOME/eureka/* 
    echo "Permisos de ejecución añadidos a todos los archivos en el directorio eureka."

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

# Reemplazar el contenido de bash.bashrc con la nueva interfaz
> "$FILE_PATH" # Vaciar el archivo bash.bashrc
echo "$NEW_LINES" > "$FILE_PATH" # Agregar el nuevo contenido
echo "bash.bashrc ha sido reemplazado con la nueva interfaz y configuración."
