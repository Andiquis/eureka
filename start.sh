#!/bin/bash

# ==============================
# CONFIGURACIÓN bash.bashrc
# ==============================

FILE_PATH="$PREFIX/etc/bash.bashrc"

# Reemplazar el contenido de bash.bashrc con una nueva configuración
CONFIG_BASH=$(cat <<'EOF'
# =============================#
#      🌟 ANDI SYSTEM 🌟      #
# =============================#

# Configuración del historial de comandos
shopt -s histappend
shopt -s histverify
export HISTCONTROL=ignoreboth

# Personalización del prompt con colores.
PROMPT_DIRTRIM=2
PS1='\e[1;34m \e[1;32m\w\e[1;36m ➜ \e[1;37m'

# Interfaz visual al iniciar la terminal
clear
echo -e "\033[1;36m========================================\033[0m"
echo -e "\033[1;33m        🚀 BIENVENIDO A ANDI 🚀        \033[0m"
echo -e "\033[1;36m========================================\033[0m"

echo -e "\033[1;34m █████╗ ███╗   ██╗██████╗ ██╗ \033[0m"
echo -e "\033[1;34m██╔══██╗████╗  ██║██╔══██╗██║ \033[0m"
echo -e "\033[1;34m███████║██╔██╗ ██║██║  ██║██║ \033[0m"
echo -e "\033[1;34m██╔══██║██║╚██╗██║██║  ██║██║ \033[0m"
echo -e "\033[1;34m██║  ██║██║ ╚████║██████╔╝███████╗ \033[0m"
echo -e "\033[1;34m╚═╝  ╚═╝╚═╝  ╚═══╝╚═════╝ ╚══════╝ \033[0m"

echo -e "\n\033[1;32m🌟 Proyecto: \033[0mEureka"
echo -e "\033[1;32m👤 Autor: \033[0mAndi"
echo -e "\033[1;32m🔌 Puerto usado: \033[0m8000"
echo -e "\033[1;32m📝 Descripción: \033[0mSistema de Restobar"
echo -e "\033[1;32m🟢 Estado: \033[0mServidor corriendo"

# Verificar instalación de PHP y SQLite3
if command -v php >/dev/null 2>&1 && command -v sqlite3 >/dev/null 2>&1; then
    echo -e "\033[1;32m✔ PHP y SQLite3 están instalados.\033[0m"
else
    echo -e "\033[1;31m✖ ERROR: PHP y SQLite3 no están instalados. Instálalos para continuar.\033[0m"
fi

# Verificar existencia de la base de datos y crearla si no existe
DB_PATH="$HOME/eureka/restaurante.db"
if [ ! -f "$DB_PATH" ]; then
    echo -e "\033[1;36m📂 Creando la base de datos vocabulario.db...\033[0m"
    php "$HOME/eureka/db_create.php"
    echo -e "\033[1;32m✔ Base de datos creada.\033[0m"
else
    echo -e "\033[1;34m✔ Base de datos encontrada.\033[0m"
fi

# Dar permisos de ejecución a archivos en cingles
chmod +x $HOME/eureka/*
echo -e "\033[1;32m✔ Permisos de ejecución añadidos.\033[0m"

# Iniciar el servidor PHP automáticamente
cd $HOME/eureka || exit
php -S 0.0.0.0:8000 -t $HOME/eureka &


# Abrir la URL en Google Chrome
am start -n com.android.chrome/com.google.android.apps.chrome.Main -d http://localhost:8000/index_database.php &
EOF
)

# Sobrescribir bash.bashrc con la nueva configuración
echo "$CONFIG_BASH" > "$FILE_PATH"
echo "✅ bash.bashrc actualizado. Reinicia Termux para aplicar los cambios."

# ==============================
# MENÚ INTERACTIVO PARA EL USUARIO
# ==============================

while true; do
    clear
    echo -e "\033[1;36m====================================\033[0m"
    echo -e "\033[1;33m     🌍 SERVIDOR PHP - TERMUX 🚀     \033[0m"
    echo -e "\033[1;36m====================================\033[0m"
    echo -e "\033[1;34m 1) \033[1;32mIniciar servidor\033[0m"
    echo -e "\033[1;34m 2) \033[1;31mDetener servidor\033[0m"
    echo -e "\033[1;34m 3) \033[1;33mAbrir en navegador\033[0m"
    echo -e "\033[1;34m 4) \033[1;31mSalir\033[0m"
    echo -e "\033[1;36m====================================\033[0m"

    read -p $'\033[1;37mSeleccione una opción: \033[0m' opcion

    case $opcion in
        1)
            echo -e "\033[1;32m🚀 Iniciando servidor en el puerto 8001...\033[0m"
            php -S 0.0.0.0:8000 -t $HOME/eureka &
            sleep 2
            ;;
        2)
            echo -e "\033[1;31m🛑 Deteniendo servidor...\033[0m"
            pkill -f "php -S 0.0.0.0:8000"
            sleep 2
            ;;
        3)
            echo -e "\033[1;33m🔄 Abriendo en el navegador...\033[0m"
            am start -n com.android.chrome/com.google.android.apps.chrome.Main -d http://localhost:8000/index_database.php &
            ;;
        4)
            echo -e "\033[1;31m👋 Saliendo...\033[0m"
            exit 0
            ;;
        *)
            echo -e "\033[1;31m❌ Opción inválida. Inténtalo de nuevo.\033[0m"
            sleep 2
            ;;
    esac
done
