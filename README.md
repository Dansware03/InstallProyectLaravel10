# Laravel Installer Package

Un asistente de instalación interactivo y visualmente atractivo para aplicaciones **Laravel 10**. Facilita la configuración inicial del proyecto con opciones de instalación rápida y avanzada, guiando al usuario paso a paso con una interfaz moderna y amigable.

[![Última Versión Estable](https://img.shields.io/packagist/v/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Total de Descargas](https://img.shields.io/packagist/dt/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Licencia](https://img.shields.io/packagist/l/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Pruebas](https://img.shields.io/github/actions/workflow/status/Dansware03/laravelinstaller/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Dansware03/laravelinstaller/actions)

---

## 🌟 ¿Por qué Laravel Installer?

Configurar un nuevo proyecto Laravel puede involucrar múltiples pasos manuales. Este paquete simplifica ese proceso a través de un asistente web intuitivo, ideal tanto para desarrolladores experimentados que buscan rapidez como para aquellos nuevos en Laravel que desean una guía visual.

---

## 🚩 Características Principales

* ✨ **Interfaz Moderna y Amigable**: Una UI clara y atractiva, con un diseño de "ventana de instalador" y barra de progreso, que guía al usuario en cada etapa.
* 🚀 **Instalación Rápida**: Configuración automatizada con ajustes predeterminados optimizados. Ideal para poner en marcha rápidamente un entorno de producción estándar.
* 🛠️ **Instalación Avanzada**: Ofrece control granular sobre la configuración de la base de datos, ejecución de migraciones, ajustes del entorno (`.env`), configuración de correo (SMTP), y más.
* ✅ **Verificación Automática de Requisitos**: Comprueba la versión de PHP, las extensiones necesarias y los permisos de directorio antes de iniciar la instalación.
* 🔒 **Seguridad Integrada**:
    *   **Middleware de Protección**: Asegura que la aplicación no sea accesible hasta que la instalación se complete.
    *   **Archivo de Bloqueo**: Crea un archivo `storage/installed` para prevenir la reinstalación accidental.
    *   **Ajustes Seguros por Defecto**: Aplica configuraciones como `APP_DEBUG=false` y `APP_ENV=production` en el modo de producción.
    *   **Generación de `APP_KEY`**: Asegura que la clave de aplicación esté configurada.
* ⚙️ **Configuración Detallada y Flexible**:
    *   Soporte para configuración de **SMTP**.
    *   Elección entre entorno de **desarrollo o producción** con optimizaciones específicas.
    *   Opción para **ejecutar o omitir migraciones**.
    *   Habilitación/deshabilitación de **rutas API** (`routes/api.php`).
    *   Aplicación de **optimizaciones de caché** de Laravel (`config:cache`, `route:cache`, `view:cache`).
* 👤 **Creación Automática de Usuario Administrador**: Si se ejecutan las migraciones, se crea un usuario administrador con credenciales temporales.
* 💻 **Comandos Artisan Útiles**:
    *   `php artisan installer:install`: Guía para iniciar el proceso de instalación web.
    *   `php artisan installer:reset`: Permite reiniciar el proceso de instalación eliminando el archivo de bloqueo.
* 🎨 **Totalmente Personalizable**: Publica y modifica la configuración, las vistas Blade y los assets (CSS/JS) para adaptar el instalador a tus necesidades.
*   **Enfoque en MySQL/MariaDB**: Actualmente optimizado para bases de datos MySQL/MariaDB.

---

## 📦 Requisitos del Sistema

*   PHP `^8.2`
*   Laravel `10.x`
*   Servidor de Base de Datos MySQL o MariaDB.
*   Extensiones de PHP comúnmente requeridas por Laravel:
    *   `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `curl`, `gd`, `zip`. (El instalador verificará estas).
*   Permisos de escritura en los siguientes directorios (relativos a la raíz del proyecto):
    *   `storage/app/`
    *   `storage/framework/`
    *   `storage/logs/`
    *   `bootstrap/cache/`
    *   El archivo `.env` debe ser escribible por el proceso del servidor web durante la instalación.

---

## ⚙️ Guía de Instalación y Configuración

Sigue estos pasos para integrar el Asistente de Instalación en tu proyecto Laravel:

### 1. Instalar el Paquete

Añade el paquete a tu proyecto usando Composer:

```bash
composer require dansware03/laravelinstaller
```

### 2. Publicar Archivos de Configuración

Es **altamente recomendado** publicar el archivo de configuración. Esto te permite personalizar aspectos clave del instalador.

```bash
php artisan vendor:publish --tag=installer-config
```
Esto creará el archivo `config/installer.php`. Revísalo para ajustar opciones como el prefijo de la ruta del instalador, los requisitos específicos, los permisos de directorio y las credenciales del usuario administrador por defecto.

### 3. Publicar Vistas y Assets (Opcional)

Si deseas modificar la apariencia del instalador o sus textos:

*   **Para las vistas (archivos Blade):**
    ```bash
    php artisan vendor:publish --tag=installer-views
    ```
    Las vistas se publicarán en `resources/views/vendor/installer/`.

*   **Para los assets (CSS/JS, aunque el paquete usa principalmente CDNs para Bootstrap/FontAwesome):**
    ```bash
    php artisan vendor:publish --tag=installer-assets
    ```
    Los assets se publicarán en `public/vendor/installer/`.

### 4. Configurar el Archivo `.env`

Asegúrate de que tu aplicación Laravel tenga un archivo `.env`. Si no existe (por ejemplo, en un despliegue nuevo), cópialo desde `.env.example`:

```bash
cp .env.example .env
```
El instalador escribirá en este archivo. **Importante:** El instalador intentará generar una `APP_KEY` si no está definida.

### 5. Aplicar el Middleware de Protección (Crucial)

Para asegurar que tu aplicación no sea accesible antes de que el proceso de instalación se haya completado, debes registrar el `InstallationMiddleware`.

Abre tu archivo `app/Http/Kernel.php` y añade el middleware al grupo `web`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... otros middlewares
        \dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class,
    ],

    // ...
];
```

Este middleware redirigirá automáticamente a los usuarios al asistente de instalación si la aplicación aún no está instalada. Una vez instalada, impedirá el acceso al asistente.

---

## 🚀 Uso del Instalador

1. Abre tu navegador en la raíz de tu proyecto (ej. `http://localhost` o `http://tu-dominio.test`)
2. Serás redirigido automáticamente a `/install`
3. Si no ocurre, navega manualmente a:

```
http://tu-dominio.test/install
```

4. Sigue el asistente paso a paso

---

## 🧰 Tipos de Instalación

### 🚀 Rápida

* Requisitos verificados automáticamente
* Configura base de datos con prueba en tiempo real
* Ejecuta migraciones
* Crea usuario administrador
* Aplica optimizaciones
* Modo: Producción por defecto

### 🛠️ Avanzada

* Control total: migraciones, SMTP, entorno (dev/prod), rutas API
* Pasos adicionales: entorno, ajustes finales, seguimiento de tareas

---

## 💻 Comandos Artisan

```bash
php artisan installer:install
```

> Proporciona guía de instalación web (no instala por CLI).

```bash
php artisan installer:reset
```

> Elimina `storage/installed` para permitir reinstalación.

---

## 🔐 Consideraciones de Seguridad

*   **Archivo de Bloqueo**: El archivo `storage/installed` previene que el instalador se ejecute de nuevo una vez completada la instalación.
*   **Middleware de Protección**: Como se mencionó, el `InstallationMiddleware` es clave para proteger tu aplicación antes de la instalación y el instalador después de ella.
*   **Credenciales del Administrador**: Si optas por la creación automática del usuario administrador, **cambia sus credenciales inmediatamente** después del primer inicio de sesión. Las credenciales por defecto se especifican en `config/installer.php` (si publicaste la configuración) o son las predeterminadas del paquete.
*   **APP_KEY**: El instalador se asegura de que una `APP_KEY` sea generada y guardada en tu archivo `.env`.
*   **Uso en Producción**:
    *   **Opción 1 (Recomendada): Desinstalar el Paquete**: Una vez que tu aplicación esté instalada y configurada en producción, la práctica más segura es desinstalar el paquete:
        ```bash
        composer remove dansware03/laravelinstaller
        ```
        Asegúrate también de eliminar el middleware de `app/Http/Kernel.php`.
    *   **Opción 2: Deshabilitar Rutas del Instalador**: Si prefieres mantener el paquete pero deshabilitar el acceso al instalador, puedes comentar la línea ` $this->loadRoutesFrom(__DIR__.'/../routes/web.php');` dentro del método `boot()` de `dansware03\laravelinstaller\LaravelInstallerServiceProvider.php`. Esto requeriría modificar el código del paquete directamente en el directorio `vendor`, lo cual no es ideal ya que los cambios se perderían con `composer update`.
    *   **Opción 3: Reglas del Servidor Web**: Configura reglas en tu servidor web (Nginx, Apache) para bloquear el acceso a la ruta del instalador (por defecto `/install`) en entornos de producción.

---

## 🧩 Personalización Avanzada

*   **Configuración Central (`config/installer.php`)**: Después de publicar (`php artisan vendor:publish --tag=installer-config`), puedes modificar:
    *   La ruta base del instalador (por defecto `/install`).
    *   Los middlewares aplicados a las rutas del instalador.
    *   Los requisitos de PHP y extensiones.
    *   Los directorios y permisos a verificar.
    *   Las credenciales por defecto del usuario administrador.
    *   Las optimizaciones de producción a aplicar.
*   **Vistas Blade (`resources/views/vendor/installer/`)**: Publica con `php artisan vendor:publish --tag=installer-views` y modifica los archivos `.blade.php` para cambiar la apariencia, textos o incluso el flujo (con precaución).
*   **Assets (CSS/JS)**: Aunque el instalador usa principalmente CDNs para Bootstrap y FontAwesome para simplificar, puedes publicar los assets del paquete con `php artisan vendor:publish --tag=installer-assets` y modificar `public/vendor/installer/`. Luego, ajusta el `layout.blade.php` publicado para enlazar tus assets locales.
*   **Traducciones**: Si publicas las vistas, puedes reemplazar los textos directamente o utilizar el sistema de localización de Laravel (ej. `__('installer_messages.welcome_title')`) y crear los archivos de idioma correspondientes en `lang/vendor/installer/{locale}/messages.php`.

---

## 🧹 Desinstalación Completa

Si necesitas eliminar completamente el paquete de tu proyecto:

1.  **Elimina el Middleware**: Quita la línea `\dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class,` de tu archivo `app/Http/Kernel.php`.
2.  **Elimina Archivos Publicados**:
    *   Borra el archivo de configuración: `config/installer.php` (si fue publicado).
    *   Borra el directorio de vistas: `resources/views/vendor/installer/` (si fue publicado).
    *   Borra el directorio de assets: `public/vendor/installer/` (si fue publicado).
3.  **Elimina el Archivo de Bloqueo**: Borra el archivo `storage/installed` (si existe).
4.  **Elimina el Paquete vía Composer**:
    ```bash
    composer remove dansware03/laravelinstaller
    ```
5.  **Limpia la Caché de Laravel**: Es una buena práctica limpiar las cachés después de eliminar un paquete:
    ```bash
    php artisan optimize:clear
    ```
    O individualmente:
    ```bash
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    ```

---

## 🛠️ Solución de Problemas Comunes

* **Clase no encontrada o rutas no definidas**
  Limpia caché:

  ```bash
  php artisan optimize:clear
  ```

* **Permisos o errores de escritura**
  Verifica manualmente los permisos:

  ```bash
  sudo chmod -R 775 storage bootstrap/cache
  sudo chown -R www-data:www-data .env storage bootstrap/cache
  ```

* **Problemas de conexión a la base de datos**
  Asegúrate de que:

  * El servicio de DB esté activo
  * Credenciales y nombre de BD sean correctos
  * La BD exista
  * El usuario tenga permisos

---

## 🤝 Contribuciones

¡Bienvenidas!

1. Fork ➝ rama ➝ commits ➝ PR
2. Sigue PSR y agrega pruebas si es necesario

```bash
git checkout -b feature/nueva-funcionalidad
```

---

## 📬 Soporte

* GitHub Issues: [Abrir nuevo issue](https://github.com/Dansware03/laravelinstaller/issues)
* Email: [dansware2003@gmail.com](mailto:dansware2003@gmail.com)

---

## 📄 Licencia

Este paquete está licenciado bajo la [Licencia MIT](LICENSE).

---

## 👤 Créditos

Desarrollado con ❤️ por **Maiker Bravo**
GitHub: [@Dansware03](https://github.com/Dansware03)

---

**¿Te ha sido útil este paquete?**
🌟 ¡Dale una estrella en [GitHub](https://github.com/Dansware03/laravelinstaller)!
📣 ¿Ideas o sugerencias? ¡Abre un issue o contáctanos!