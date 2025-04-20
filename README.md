# Laravel Installer

Un asistente de instalación interactivo para aplicaciones Laravel 10, permitiendo configurar fácilmente una nueva instancia con base de datos (MySQL o SQLite) y configuraciones básicas del sistema.

## Características

- **Asistente de Instalación Guiado:** Interfaz paso a paso para la configuración del sistema
- **Verificación de Requisitos:** Comprobación automática de requisitos del sistema
- **Configuración Flexible de Base de Datos:** Soporte para MySQL y SQLite, con posibilidad de probar la conexión en tiempo real
- **Migración Automática:** Ejecución de migraciones y seeders tras la configuración
- **Modo de Entorno:** Configuración adaptable para entornos de producción o desarrollo
- **API REST Segura:** Implementación de autenticación basada en tokens usando Laravel Sanctum

## Instalación

1. Agregar el paquete a tu proyecto Laravel

```bash
composer require Dansware03/laravel-installer
```

2. Registra el middleware global en `app/Http/Kernel.php`:

```php
protected $middleware = [
    // Otros middlewares...
    \Dansware\LaravelInstaller\Middleware\CheckInstallationMiddleware::class,
];
```

3. Publica la configuración (opcional pero recomendado):

```bash
php artisan vendor:publish --tag=installer-config
```

4. Publica los assets (opcional):

```bash
php artisan vendor:publish --tag=installer-assets
```

### Personalización

Puedes personalizar varios aspectos del instalador editando el archivo de configuración `config/installer.php` después de publicarlo:

- Cambiar la ruta del instalador (por defecto es `/install`)
- Modificar el título y logo del instalador
- Ajustar los requisitos del servidor
- Personalizar los pasos del instalador

### Consideraciones importantes:

- Asegúrate de que el directorio `storage` tenga permisos de escritura para que se pueda crear el archivo `.installed`
- Si estás usando un sistema de control de versiones, considera agregar el archivo `.installed` al `.gitignore`
- El instalador modificará tu archivo `.env` para configurar la base de datos y otros ajustes básicos

### Flujo de uso:

1. Instala una nueva aplicación Laravel
2. Instala el paquete instalador
3. Accede a tu aplicación por primera vez, serás redirigido automáticamente al instalador
4. Sigue los pasos del asistente:
   - Bienvenida
   - Verificación de requisitos
   - Configuración de base de datos
   - Finalización y ejecución de migraciones
5. Una vez completada la instalación, serás redirigido a la página principal de tu aplicación

### Solución de problemas:

Si necesitas reiniciar el proceso de instalación, simplemente elimina el archivo `.installed` de tu directorio `storage`.

```bash
rm storage/.installed
```

### Desarrollo avanzado:

Si deseas extender la funcionalidad del instalador, puedes publicar las vistas y modificarlas:

```bash
php artisan vendor:publish --tag=installer-views
```

## Requisitos

- PHP >= 8.2
- Laravel 10.x

## Estructura del paquete
```
laravel-installer/
├── composer.json
├── README.md
├── LICENSE.md
├── src/
│   ├── InstallationServiceProvider.php
│   ├── Controllers/
│   │   └── InstallationController.php
│   ├── helpers.php
│   ├── routes/
│   │   └── web.php
│   └── resources/
│       ├── views/
│       │   ├── layouts/
│       │   │   └── installer.blade.php
│       │   ├── installation/
│       │   │   ├── welcome.blade.php
│       │   │   ├── requirements.blade.php
│       │   │   ├── database.blade.php
│       │   │   └── finish.blade.php
│       │   └── components/
│       │       ├── button.blade.php
│       │       ├── input.blade.php
│       │       └── alert.blade.php
│       └── assets/
│           ├── css/
│           │   └── installer.css
│           └── js/
│               └── installer.js
```
## Licencia

Este paquete está bajo la [Licencia MIT](LICENSE.md).