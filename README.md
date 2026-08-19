<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://www.trakio.pro/img/logo_temp.png" width="200" alt="Laravel Logo"></a></p>



# Trakio

# Descripción

**Trakio** es una aplicación para la gestión y administración integral del proceso GESI, para la Subred Integrada de Servicios de Salud Norte

# Requerimientos del Proyecto

A continuación se presenta una lista de las dependencias requeridas para el funcionamiento del proyecto, junto con una breve descripción de cada una y su propósito.

##Habilitar la extencion gd en php para instalar dependencias
 *Ve a C:\xampp\php\php.ini y ábrelo en un editor de texto.
    ;extension=gd
    
*Quita el punto y coma (;) al inicio de la línea para que quede así:
     extension=gd

*Realiza el mismo procedimiento con 
    ;extension=zip



    

## 1. Dependencias Principales

| Paquete                         | Descripción                                                                                     |
|--------------------------------|-------------------------------------------------------------------------------------------------|
| `barryvdh/laravel-debugbar`    | Herramienta para depuración en Laravel que proporciona información sobre el rendimiento y la ejecución de las consultas. |
| `barryvdh/laravel-dompdf`      | Generador de PDF basado en la biblioteca DOMPDF para crear documentos PDF a partir de HTML.  |
| `barryvdh/laravel-snappy`      | Permite la generación de PDF usando Snappy (wkhtmltopdf) desde vistas de Laravel.             |
| `fpdf/fpdf`                    | Biblioteca para generar archivos PDF en PHP.                                                  |
| `laravel/breeze`               | Paquete que proporciona un sistema de autenticación simple y moderno para aplicaciones de Laravel. |
| `laravel/reverb`               | Permite la integración de Laravel con el paquete Reverb para manejar eventos.                   |
| `laravel/sail`                 | Entorno de desarrollo ligero basado en Docker para Laravel.                                   |
| `laravel/tinker`               | Herramienta de consola para interactuar con tu aplicación Laravel mediante un REPL.           |
| `laravel/ui`                   | Proporciona una interfaz de usuario básica y generación de autenticación para aplicaciones Laravel. |
| `maatwebsite/excel`            | Paquete para importar y exportar archivos Excel y CSV en Laravel.                            |
| `nesbot/carbon`                | Biblioteca para trabajar con fechas y horas de manera más sencilla y poderosa en PHP.        |
| `nunomaduro/collision`         | Proporciona una interfaz de usuario elegante para mostrar errores en la consola de Laravel.    |
| `nunomaduro/termwind`          | Permite la creación de interfaces de usuario en terminal de forma sencilla y efectiva.        |
| `spatie/laravel-ignition`      | Herramienta de depuración para Laravel que ofrece un manejo de excepciones mejorado y más amigable. |
| `vinkla/hashids`               | Implementación de Hashids para PHP, permite enmascarar IDs numéricos en cadenas únicas.      |
| `tightenco/ziggy`              | Permito usar route() en JavaScript




# Instalación de dependencias

1. Instalación con Composer
   Ejecutar dentro de la carpeta del proyecto
   ```bash
   composer install

2. Node JS (instalar de manera global en servidores Linux)

   Instalar el repositorio de NodeSource para la versión de Node.js
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
   sudo apt-get install -y nodejs

4. Puppeteer v23.3.0
   ```bash
   npm install puppeteer@23.3.0

5. Instalar Puppeteer y otras dependencias globalmente 
   ```bash
   sudo npm install -g puppeteer
   
3. Instalación de spatie/browsershot
   ```bash
   composer require spatie/browsershot
   
4. Instalar Chromium
   ```bash
   sudo apt-get install chromium

5. Instalación de Dependencias

Para instalar estas dependencias, puedes usar Composer. Ejecuta el siguiente comando en la raíz de tu proyecto:

   
    composer require barryvdh/laravel-debugbar
    composer require barryvdh/laravel-dompdf
    composer require barryvdh/laravel-snappy
    composer require fpdf/fpdf
    composer require laravel/breeze
    composer require laravel/reverb
    composer require laravel/sail
    composer require laravel/tinker
    composer require laravel/ui
    composer require maatwebsite/excel
    composer require nesbot/carbon
    composer require nunomaduro/collision
    composer require nunomaduro/termwind
    composer require spatie/laravel-ignition
    composer require vinkla/hashids
    composer require tightenco/ziggy
   
# Configuración del entorno
1. Copiar el archivo `env.example` a `.env`
   ```bash
    cp .env.example .env

3. Generar clave de aplicación
   ```bash
   php artisan key:generate

4. Configurar el archivo .env con los detalles de la base de datos
5. Generar enlace simbólico
   ```bash
   php artisan storage:link

# Configuración de la base de datos
1. Crear la base de datos en el gestor de bases de datos (MySQL, PostgreSQL).
2. Ejecutar las migraciones
   ```bash
   php artisan migrate
3. Cargar datos iniciales de la base de datos
   ```bash
   php artisan db:seed

## Ejecución de tareas programadas
1. Verificar si existen tareas programadas
   ```bash
   php artisan schedule:list

2. Ver el estado de las tareas en el sistema (cron jobs)
   ```bash
   crontab -l

3. Si la respues es "no crontab for trakio" significa que no se ha configurado ningún cron job, entonces se ejecuta el siguiente comando para abrir el archivo crontab
   ```bash
   crontab -e

4. Agregar la siguiente línea al archivo crontab para que Laravel ejecute el scheduler cada minuto
   ```bash
   * * * * * cd /home/trakio/htdocs/www.trakio.pro/gesilaravel && php artisan schedule:run >> /dev/null 2>&

# Eventos Programados en MySQL

## 1. Eliminación de registros en `productivity_sds` cada 4 horas

**Descripción**: Este evento elimina todos los registros de la tabla `productivity_sds` que tienen un campo `updated_at` mayor a 4 horas desde la última actualización. El evento se ejecuta automáticamente cada 4 horas.

- **Evento**: `delete_old_records`
- **Programación**: Cada 4 horas
- **Consulta SQL utilizada**:
    ```sql
    CREATE EVENT IF NOT EXISTS delete_old_records
    ON SCHEDULE EVERY 4 HOUR
    DO
      DELETE FROM `productivity_sds`
      WHERE `updated_at` < NOW() - INTERVAL 4 HOUR;
    ```
- **Objetivo**: Mantener la tabla `productivity_sds` limpia eliminando registros antiguos que ya no sean necesarios.
- **Fecha de inicio**: (coloca aquí la fecha de creación del evento)
  
## 2. Eliminación de notificaciones de cumpleaños del día anterior

**Descripción**: Este evento elimina las notificaciones de cumpleaños que se crearon el día anterior para evitar que se acumulen notificaciones antiguas.

- **Evento**: `delete_old_birthday_notifications`
- **Programación**: Diariamente
- **Consulta SQL utilizada**:
    ```sql
    CREATE EVENT IF NOT EXISTS delete_old_birthday_notifications
    ON SCHEDULE EVERY 1 DAY
    STARTS CURRENT_TIMESTAMP + INTERVAL 1 DAY
    DO
      DELETE FROM `notificacion_cumpleaños`
      WHERE `created_at` < CURDATE() - INTERVAL 1 DAY;
    ```
- **Objetivo**: Borrar las notificaciones de cumpleaños que ya han pasado y mantener limpia la tabla `notificacion_cumpleaños`.
- **Fecha de inicio**: (coloca aquí la fecha de creación del evento)

## 3. Eliminación de notificaciones antiguas

**Descripción**: Este evento elimina registros de la tabla `notifications` si el `status` es diferente a 0 o si `updated_at` es mayor a 4 días.

- **Evento**: `delete_old_notifications`
- **Programación**: Diariamente
- **Consulta SQL utilizada**:
    ```sql
    CREATE EVENT IF NOT EXISTS delete_old_notifications
    ON SCHEDULE EVERY 1 DAY
    DO
      DELETE FROM `notifications`
      WHERE `status` <> 0 OR `updated_at` < NOW() - INTERVAL 4 DAY;
    ```
- **Objetivo**: Mantener la tabla `notifications` limpia eliminando registros innecesarios o antiguos.
- **Fecha de inicio**: (coloca aquí la fecha de creación del evento)

# Solución de errores comunes
Error: require(C:/xampp/htdocs/gesilaravel-main/vendor/composer/../symfony/clock/Resources/now.php): Failed to open stream
Puede ocurrir debido a problemas en la instalación de Composer. Para solucionarlo:
1. Eliminar la carpeta "vendor" y el archivo "composer.lock":
   ```bash
   rm -rf vendor composer.lock

2. Reinstalación de dependencias
   ```bash
   composer install

3. Limpiar caché de ser necesario:
   ```bash
   composer clear-cache

- **Ejecución del servidor
1. Iniciar el servidor
    ```bash
    php artisan serve
    
2. Acceder a la aplicación con la ruta especificada en la terminal, por ejemplo `http://localhost:8000`

# Api`s

1. Instala Laravel Sanctum en tu proyecto ejecutando este comando en la terminal:

 composer require laravel/sanctum
    
2. Publica el archivo de configuración de Sanctum:
    ```bash
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

3. en Bootstrap app dentro de `withMiddleware` agrega este middleware para solicitudes API

    ```bash
    use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Dentro de `withMiddleware` agrega este middleware para solicitudes API.
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(EnsureFrontendRequestsAreStateful::class);
})

4.En tu archivo .env, si usas una URL o dominio específico, asegúrate de que esté presente en la variable SANCTUM_STATEFUL_DOMAINS, así:

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

# Versionado de archivos con Vite (producción)
1. Crear el archivo del servicio laravel-vite.service
Primero, crea un archivo para el servicio de laravel-vite en systemd.


1. Crear el archivo de servicio
    ```bash
    sudo nano /etc/systemd/system/laravel-vite.service

    
2. Contenido del archivo laravel-vite.service
Dentro del archivo, agrega el siguiente contenido:
    ```bash
   [Unit]
    Description=Laravel Vite Development Server
    After=network.target
    
    [Service]
    User=trakio
    WorkingDirectory=/home/trakio/htdocs/www.trakio.pro/gesilaravel
    ExecStart=/usr/bin/npm run dev
    Restart=always
    RestartSec=10
    StandardOutput=syslog
    StandardError=syslog
    
    [Install]
    WantedBy=multi-user.target


Explicación de cada sección:
[Unit]:

Description: Descripción del servicio.
After: Define que el servicio debe iniciarse después de que se haya establecido la red.
[Service]:

User: El usuario bajo el cual se ejecutará el servicio. Asegúrate de usar el usuario correcto, en este caso trakio.
WorkingDirectory: La ruta del directorio donde se encuentra tu proyecto Laravel.
ExecStart: El comando que se ejecutará para iniciar el servidor de desarrollo Vite. Aquí se usa npm run dev para iniciar Vite.
Restart: Define que el servicio debe reiniciarse siempre que falle.
RestartSec: El tiempo en segundos antes de reiniciar el servicio después de que falle.
StandardOutput y StandardError: Redirigen la salida estándar y los errores al registro del sistema (syslog).
[Install]:

WantedBy: Indica que el servicio debe ejecutarse en el objetivo multi-user.target, lo que significa que se ejecutará durante el arranque del sistema en un entorno multiusuario.


2. Recargar systemd y habilitar el servicio
Después de crear y guardar el archivo de servicio, ejecuta los siguientes comandos para recargar systemd, habilitar y arrancar el servicio:

1. Recargar systemd para reconocer el nuevo servicio
    ```bash
    sudo systemctl daemon-reload
2. Habilitar el servicio para que se inicie automáticamente al arrancar el sistema
   ```bash
    sudo systemctl enable laravel-vite.service
3. Iniciar el servicio
   ```bash
   sudo systemctl start laravel-vite.service
4. Verificar el stado del servicio
    ```bash
    sudo systemctl status laravel-vite.service
5. Deberías ver algo similar a lo siguiente:
    
    ```bash
    ● laravel-vite.service - Laravel Vite Development Server
     Loaded: loaded (/etc/systemd/system/laravel-vite.service; enabled; vendor preset: enabled)
     Active: active (running) since ...
     ...
6. Verificar los logs del servicio
Si el servicio no está funcionando correctamente, puedes revisar los logs para encontrar detalles de cualquier error:
      ```
    sudo journalctl -u laravel-vite.service -b
  
## Eliminar el servicio (si es necesario)
1. Detener servicio
     ```bash
     sudo systemctl stop laravel-vite.service
2. Deshabilitar el servicio
    ```bash
    sudo systemctl disable laravel-vite.service
3. Eliminar el archivo de servicio
    ```bash
    sudo rm /etc/systemd/system/laravel-vite.service
4. recargar nsystemd despues de eliminar el archivo
    ```bash
    sudo systemctl daemon-reload

    

   
    
