# Laravel Installer Package

Un asistente de instalación interactivo y visualmente atractivo para aplicaciones Laravel 10. Facilita la configuración inicial del proyecto con opciones de instalación rápida y avanzada, guiando al usuario a través de cada paso con una interfaz moderna y amigable.

[![Última Versión Estable](https://img.shields.io/packagist/v/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Total de Descargas](https://img.shields.io/packagist/dt/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Licencia](https://img.shields.io/packagist/l/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)

## Características Destacadas

✨ **Interfaz Moderna y Amigable**: Un diseño de interfaz de usuario cuidadosamente elaborado para una experiencia de instalación agradable, con un layout lateral que guía el proceso paso a paso.
🚀 **Instalación Rápida**: Configuración automática con ajustes predeterminados para un inicio veloz. Ideal para producción.
🛠️ **Instalación Avanzada**: Control granular sobre cada aspecto de la configuración, desde la base de datos hasta el entorno y optimizaciones.
✅ **Verificación de Requisitos Completa**: Comprobación automática de la versión de PHP, extensiones requeridas y permisos de directorio.
🔒 **Seguridad Integrada**:
    - Middleware para proteger la aplicación antes de la instalación.
    - Aplicación de configuraciones de seguridad para producción (desactivación de debug, etc.).
    - Creación de archivo de bloqueo `storage/installed` para prevenir reinstalaciones.
⚙️ **Configuración Detallada**:
    - **Base de Datos**: Soporte para MySQL con prueba de conexión en tiempo real.
    - **Migraciones**: Opción para ejecutar migraciones durante la instalación.
    - **Entorno**: Configuración del nombre de la aplicación, detalles de correo electrónico (SMTP), y selección del tipo de entorno (desarrollo/producción).
    - **Optimizaciones**: Aplicación automática de caché de configuración, rutas y vistas para entornos de producción.
    - **API**: Opción para deshabilitar las rutas de API (`routes/api.php`).
👤 **Usuario Administrador**: Creación automática de un usuario administrador con credenciales temporales si se ejecutan las migraciones.
💻 **Comandos de Consola**:
    - `php artisan installer:install`: Guía al usuario sobre cómo iniciar el proceso de instalación web.
    - `php artisan installer:reset`: Elimina el archivo de bloqueo `storage/installed` para permitir una nueva instalación.
🎨 **Personalizable**: Publica vistas y assets para adaptar completamente el instalador a la imagen de tu proyecto.

## Requisitos

- PHP ^8.2
- Laravel Framework 10.x
- Extensiones de PHP (verificadas por el instalador):
  `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `curl`, `gd`, `zip`
- Permisos de escritura en los directorios: `storage/app/`, `storage/framework/`, `storage/logs/`, `bootstrap/cache/` (verificados por el instalador).

## Instalación

1.  **Instalar el paquete vía Composer:**
    ```bash
    composer require dansware03/laravelinstaller
    ```

2.  **Publicar archivos del paquete:**
    *   **Configuración (obligatorio):**
        ```bash
        php artisan vendor:publish --tag=installer-config
        ```
        Esto creará el archivo `config/installer.php` donde puedes personalizar varios aspectos del instalador.

    *   **Vistas (opcional, para personalización visual):**
        ```bash
        php artisan vendor:publish --tag=installer-views
        ```
        Las vistas se publicarán en `resources/views/vendor/installer/`.

    *   **Assets (opcional, si no deseas usar CDN para Bootstrap/FontAwesome o necesitas personalización profunda):**
        ```bash
        php artisan vendor:publish --tag=installer-assets
        ```
        Los assets (CSS, JS, imágenes) se publicarán en `public/vendor/installer/`. Por defecto, el paquete utiliza Bootstrap 5 y Font Awesome 6 desde CDN para un rendimiento óptimo.

3.  **Configurar archivo `.env`:**
    Asegúrate de que exista un archivo `.env` en la raíz de tu proyecto Laravel. Si no es así, puedes crearlo copiando el archivo de ejemplo:
    ```bash
    cp .env.example .env
    ```
    El instalador intentará actualizar este archivo con la configuración proporcionada.

4.  **Configurar el Middleware de Protección (Recomendado):**
    Para proteger tu aplicación y redirigir automáticamente al instalador si la aplicación aún no está configurada, añade el middleware `InstallationMiddleware` al grupo `web` en tu archivo `app/Http/Kernel.php`:
    ```php
    protected $middlewareGroups = [
        'web' => [
            // ... otros middlewares
            \dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class,
        ],
        // ...
    ];
    ```
    Alternativamente, si deseas proteger todas las rutas de tu aplicación (incluyendo API, si no se deshabilita), puedes añadirlo al array global `$middleware`. Asegúrate de colocarlo después de middlewares esenciales como `StartSession`, `VerifyCsrfToken`, etc.
    ```php
    protected $middleware = [
       // ... otros middlewares globales
       \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
       \App\Http\Middleware\TrimStrings::class,
       \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
       // ...
       \dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class, // Añadir aquí o al final
    ];
    ```
    **Nota:** La recomendación general es añadirlo al grupo `web` para la mayoría de las aplicaciones.

## Uso

1.  **Accede a tu aplicación:**
    Abre la URL raíz de tu aplicación Laravel en un navegador web.
    Si el middleware está correctamente configurado y la aplicación no ha sido instalada, serás redirigido automáticamente a la página de bienvenida del instalador (por defecto `/install`).

2.  **Acceso Directo (si la redirección automática no ocurre):**
    Puedes navegar directamente a la URL de instalación:
    ```
    http://tu-dominio.test/install
    ```
    (Reemplaza `tu-dominio.test` con el dominio real de tu aplicación).

3.  **Sigue el Asistente de Instalación:**
    La interfaz te guiará a través de los diferentes pasos, ya sea que elijas la instalación rápida o la avanzada.

## Tipos de Instalación

### 🚀 Instalación Rápida
Ideal para poner tu aplicación en funcionamiento rápidamente con configuraciones optimizadas, generalmente para producción:
*   **Verificación Automática de Requisitos**: El sistema verifica PHP, extensiones y permisos.
*   **Configuración de Base de Datos**: Ingresa los detalles de tu base de datos MySQL. La conexión se prueba en tiempo real.
*   **Proceso Automatizado**: El instalador ejecuta migraciones, crea el usuario administrador, aplica configuraciones de seguridad y optimizaciones de producción.
*   **Credenciales**: Al finalizar, se muestran las credenciales del usuario administrador.
*   **Pasos**: Bienvenida -> Requisitos (si fallan) -> Base de Datos -> Instalando -> Completado.

### 🛠️ Instalación Avanzada
Proporciona control total sobre cada aspecto de la configuración inicial:
*   **Verificación Detallada de Requisitos**: Revisa cada requisito y su estado.
*   **Configuración de Base de Datos**: Similar a la rápida, pero como un paso dedicado.
*   **Migraciones**: Decide si ejecutar las migraciones de la base de datos. Omitir esto también omitirá la creación del usuario administrador.
*   **Configuración de Entorno**: Define el nombre de la aplicación y configura los ajustes de correo electrónico (SMTP).
*   **Configuración Final**: Elige el tipo de entorno (desarrollo o producción), lo que afecta las optimizaciones y el modo debug. También puedes optar por deshabilitar las rutas de API.
*   **Instalación Detallada**: Observa el progreso de cada tarea de configuración.
*   **Pasos**: Bienvenida -> Requisitos -> Base de Datos -> Migraciones -> Entorno -> Configuración Final -> Instalando -> Completado.

## Comandos de Consola

El paquete incluye dos útiles comandos de Artisan:

*   **`php artisan installer:install`**
    Este comando no realiza la instalación por CLI. En su lugar, verifica si la aplicación ya está instalada. Si no, proporciona instrucciones claras sobre cómo acceder al instalador web.
    ```bash
    php artisan installer:install
    ```

*   **`php artisan installer:reset`**
    Este comando elimina el archivo de bloqueo `storage/installed`. Esto es útil si necesitas ejecutar nuevamente el proceso de instalación para desarrollo, pruebas o después de un error.
    ```bash
    php artisan installer:reset
    ```

## Seguridad

*   **Archivo de Bloqueo (`storage/installed`)**: Una vez que la instalación se completa exitosamente, se crea este archivo. Previene que el asistente de instalación se ejecute nuevamente, protegiendo tu configuración.
*   **Middleware de Protección**: El `InstallationMiddleware` es crucial. Asegura que ninguna parte de tu aplicación sea accesible hasta que el proceso de instalación haya finalizado.
*   **Credenciales de Administrador**: Si optas por crear un usuario administrador durante la instalación, es **imperativo** cambiar la contraseña temporal proporcionada después del primer inicio de sesión.
*   **Post-Instalación en Producción**:
    *   Se recomienda **deshabilitar el acceso a la ruta `/install`** después de una instalación exitosa en un entorno de producción. Esto se puede hacer comentando la línea ` $this->loadRoutesFrom(__DIR__.'/../routes/web.php');` en el método `boot` del `LaravelInstallerServiceProvider.php` después de la instalación, o configurando reglas específicas en tu servidor web (Nginx, Apache) para bloquear el acceso a esa ruta.
    *   Alternativamente, para mayor seguridad, puedes **desinstalar el paquete** por completo si no prevés necesitarlo nuevamente en ese entorno.

## Personalización

El instalador está diseñado para ser flexible:

*   **Configuración Central (`config/installer.php`)**: Después de publicar la configuración, puedes modificar este archivo para:
    *   Cambiar el prefijo de la ruta del instalador (por defecto `install`).
    *   Ajustar la lista de middlewares aplicados a las rutas del instalador.
    *   Modificar los requisitos de PHP y extensiones.
    *   Cambiar los directorios y permisos requeridos.
    *   Establecer diferentes credenciales por defecto para el usuario administrador.
    *   Definir qué optimizaciones de producción se aplican.
*   **Vistas Blade**: Si publicaste las vistas (`php artisan vendor:publish --tag=installer-views`), puedes editar los archivos en `resources/views/vendor/installer/` para cambiar completamente la apariencia y el flujo del instalador. Esto te permite adaptar el diseño a la marca de tu aplicación.
*   **Assets (CSS/JS)**: Si publicaste los assets (`php artisan vendor:publish --tag=installer-assets`), puedes encontrar y modificar los archivos en `public/vendor/installer/`. Esto es útil si necesitas integrar estilos personalizados o funcionalidades JavaScript.
*   **Traducciones**: Actualmente, el texto está directamente en las vistas. Para soportar múltiples idiomas, necesitarás publicar las vistas y reemplazar las cadenas de texto con las funciones de localización de Laravel (ej. `__('messages.welcome')`).

## Funciones Helper (si existen)

Actualmente, el paquete no expone funciones helper globales directamente para el uso en la aplicación principal, ya que su funcionalidad está encapsulada dentro del proceso de instalación. Las operaciones principales se manejan a través del `InstallerManager` y el `InstallerController`.

## Desinstalar el Paquete

Si ya no necesitas el instalador en tu proyecto (especialmente recomendado para entornos de producción después de una instalación exitosa), sigue estos pasos:

1.  **Eliminar el Middleware**: Si añadiste `\dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class` a tu `app/Http/Kernel.php`, elimínalo.
2.  **Eliminar Archivos Publicados (Opcional)**:
    *   Configuración: `config/installer.php`
    *   Vistas: `resources/views/vendor/installer/`
    *   Assets: `public/vendor/installer/`
3.  **Eliminar el archivo de bloqueo**: `storage/installed` (si existe).
4.  **Ejecutar `composer remove`**:
    ```bash
    composer remove dansware03/laravelinstaller
    ```
5.  **Limpiar caché (Recomendado)**:
    ```bash
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    ```

## Solución de Problemas Comunes

*   **Error: "Target class [ViewController] does not exist." o similar después de la instalación/desinstalación**:
    Asegúrate de limpiar las cachés de Laravel:
    ```bash
    php artisan optimize:clear
    ```
    O individualmente: `config:clear`, `route:clear`, `view:clear`.

*   **Error: "Route [installer.welcome] not defined."**:
    *   Verifica que el Service Provider se esté cargando.
    *   Asegúrate de que no hayas comentado accidentalmente la carga de rutas en `LaravelInstallerServiceProvider`.
    *   Si acabas de instalar el paquete, intenta `composer dump-autoload`.

*   **Problemas de Permisos durante la instalación**:
    El instalador verifica los permisos, pero si encuentras errores relacionados con la escritura de archivos (ej. `.env`, `storage/installed`), verifica manualmente los permisos de los directorios `storage/` y `bootstrap/cache/` y el archivo `.env`. Deben ser escribibles por el servidor web.
    ```bash
    sudo chmod -R 775 storage bootstrap/cache
    sudo chown -R www-data:www-data storage bootstrap/cache .env # Ajusta www-data:www-data a tu usuario/grupo de servidor web
    ```

*   **La base de datos no se conecta**:
    *   Verifica que el servidor de base de datos esté en ejecución.
    *   Confirma que las credenciales (host, puerto, nombre de base de datos, usuario, contraseña) sean correctas.
    *   Asegúrate de que la base de datos especificada exista. El instalador no la crea.
    *   Comprueba que el usuario de la base de datos tenga los permisos necesarios para conectarse y modificar la base de datos desde el host de la aplicación.

## Contribuir

¡Las contribuciones son bienvenidas! Si deseas mejorar este paquete:
1.  Realiza un Fork del proyecto en GitHub.
2.  Crea una nueva rama para tu característica o corrección: `git checkout -b feature/mi-nueva-funcionalidad` o `fix/un-bug-especifico`.
3.  Realiza tus cambios y haz commit: `git commit -am 'Añadir nueva funcionalidad increíble'`.
4.  Empuja tus cambios a tu rama: `git push origin feature/mi-nueva-funcionalidad`.
5.  Abre un Pull Request en el repositorio original.

Por favor, asegúrate de que tu código siga los estándares de codificación y añade pruebas si es aplicable.

## Soporte

Si encuentras algún problema, tienes alguna pregunta o deseas solicitar una nueva característica, no dudes en:
- Abrir un **Issue** en el repositorio de GitHub: [https://github.com/Dansware03/laravelinstaller/issues](https://github.com/Dansware03/laravelinstaller/issues)
- Contactar al desarrollador por correo electrónico: `dansware2003@gmail.com`

## Licencia

El paquete Laravel Installer es software de código abierto licenciado bajo la [MIT License (MIT)](LICENSE).

## Créditos

Este paquete ha sido desarrollado con ❤️ por **Maiker Bravo** ([@Dansware03](https://github.com/Dansware03)).

---

**¿Te ha sido útil este paquete?** Considera ⭐ darle una estrella en [GitHub](https://github.com/Dansware03/laravelinstaller) para apoyar su desarrollo.
**¿Necesitas ayuda o tienes ideas?** ¡Participa en la comunidad abriendo un issue o contactándonos!