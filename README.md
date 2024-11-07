
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
git clone https://github.com/Andiquis/eureka
ls
cd eureka
chmod +x *
ls
bash go.sh
```
## Acceso a la Aplicación

La aplicacion debio iniciarce automaticamente despues de ejecutar el archivo go.sh


¡Ya puedes disfrutar de tu aplicación web en tu teléfono!

### Notas

- Asegúrate de tener una conexión de red adecuada para acceder a la aplicación.
- Para detener el servidor, puedes usar Ctrl + C en la terminal donde lo ejecutaste. > version anterior
- para detener el servidor puedes listar procesor y eliminar el proceso de tu servidor.
```bash
ps aux
kill <PID>
```
  para visualizar su aplicacion web en otros dispositivos locales solo acceda al ip swlan0 de las red. el ip puede visualizar ejecutando el siguiente comando en termux
  ```bash
ifconfig
```
en el navegador de otro dispositivo 192.168.#.#:8002
