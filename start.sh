#!/bin/bash

# Variables
FILE_PATH="$PREFIX/etc/bash.bashrc"

# Crear contenido para la nueva interfaz en bash.bashrc
NEW_LINES=$(cat <<'EOF'
# =============================
#       Eureka Terminal 🚀
# =============================

# Configuraciones de historial de comandos
shopt -s histappend        # Adjuntar en lugar de sobrescribir el historial al salir de la shell
shopt -s histverify        # No ejecutar inmediatamente al usar sustitución de historial
export HISTCONTROL=ignoreboth  # No guardar duplicados ni comandos que inician con espacio en el historial

# Configuración de línea de comandos por defecto
PROMPT_DIRTRIM=2
PS1='\[\e[0;32m\]\w\[\e[0m\] \[\e[0;97m\]\$\[\e[0m\] '

# Manejo de comandos inexistentes
if [ -x /data/data/com.termux/files/usr/libexec/termux/command-not-found ]; then
    command_not_found_handle() {
        /data/data/com.termux/files/usr/libexec/termux/command-not-found "$1"
    }
fi

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
