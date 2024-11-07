
# Aplicación Web para adquirir vocabulario en ingles

## Descripción

Esta es una aplicación web diseñada específicamente para ejecutarse en navegadores móviles o en Termux. Es una solución ligera y eficiente para gestionar tu base de datos SQLite de manera fácil y accesible.

## Requisitos

Asegúrate de tener Termux instalado en tu dispositivo Android.

<a href="https://github.com/termux/termux-app/releases/download/v0.118.0/termux-app_v0.118.0+github-debug_universal.apk"><img src="https://img.shields.io/badge/DOWNLOAD_APK-25D366?style=for-the-badge&logo=github&logoColor=black" />

## Instalación y Ejecución

Sigue los siguientes pasos para configurar la aplicación:
 ```bash
pkg update
pkg upgrade
pkg install nano
pkg install php 
pkg install sqlite
pkg install git
git clone https://github.com/Andiquis/cingles
ls
cd cingles
chmod +x *
ls
php -S 0.0.0.0:8001
```
## Acceso a la Aplicación

Una vez que el servidor esté en ejecución, abre tu navegador móvil y accede a:
http://localhost:8001


¡Ahora puedes disfrutar de tu aplicación web en tu teléfono!

### Notas

- Asegúrate de tener una conexión de red adecuada para acceder a la aplicación.
- Para detener el servidor, puedes usar Ctrl + C en la terminal donde lo ejecutaste.


### Opcional

  -Este paso es para automatizar el encendido del servidor y iniciar la aplicacion en el navegador de manera automatica en la vamos a configurar el archivo de arranque de termux para que el servidor arranque al momento de entrar a termux
 ```bash
cd $HOME
cd ..
cd usr
cd etc
nano bash.bashrc
```
En el final del codigo de bash.bashrc adicionar las siguientes lineas de codigo
```bash
cd $HOME/cingles
bash start.sh &
am start -n com.android.chrome/com.google.android.apps.chrome.Main -d http://127.0.0.1:8001/index.php &
```
  Guarda el archivo y reinicia termux.
  
  para visualizar su aplicacion web en otros dispositivos locales solo acceda al ip swlan0 de las red. el ip puede visualizar ejecutando el siguiente comando en termux
  ```bash
ifconfig
```
en el navegador 192.168.#.#:8001
