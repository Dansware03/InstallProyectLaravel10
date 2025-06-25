# Laravel Installer Package

Un asistente de instalación interactivo y completo para aplicaciones Laravel 10, que facilita la configuración inicial con opciones de instalación rápida y avanzada.

## Características

- ✅ **Instalación Rápida**: Configuración automática con ajustes predeterminados
- ✅ **Instalación Avanzada**: Control total sobre cada aspecto de la configuración
- ✅ **Verificación de Requisitos**: Comprueba automáticamente PHP, extensiones y permisos
- ✅ **Configuración de Base de Datos**: Soporte completo para MySQL
- ✅ **Optimizaciones de Producción**: Cache automático de configuración, rutas y vistas
- ✅ **Configuraciones de Seguridad**: Desactivación de debug y configuraciones seguras
- ✅ **Usuario Administrador**: Creación automática de usuario con credenciales temporales
- ✅ **Middleware de Protección**: Previene acceso no autorizado durante la instalación
- ✅ **Comandos de Consola**: Herramientas adicionales para gestión

## Requisitos

- PHP 8.2 o superior
- Laravel 10.x
- Extensiones de PHP: bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl, gd, zip
- Permisos de escritura en: storage/, bootstrap/cache/

## Instalación

### 1. Instalar el paquete via Composer

```bash
composer require dansware03/laravel-installer
```

### 2. Publicar archivos del paquete

```bash
# Publicar configuración
php artisan vendor:publish --tag=installer-config

# Publicar vistas (opcional, para personalización)
php artisan vendor:publish --tag=installer-views

# Publicar assets (opcional)
php artisan vendor:publish --tag=installer-assets
```

### 3. Configurar archivo .env

Asegúrate de tener un archivo `.env` en tu proyecto:

```bash
cp .env.example .env
```

### 4. Configurar el middleware (opcional)

Si deseas proteger toda tu aplicación hasta que esté instalada, añade el middleware a `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ... otros middlewares
    \dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class,
];
```

## Uso

### Opción 1: Instalación Web (Recomendada)

1. Navega a tu aplicación en el navegador
2. Serás redirigido automáticamente a `/install`
3. Sigue el asistente de instalación

### Opción 2: Acceso directo

Visita directamente la URL de instalación:

```
http://tu-dominio.com/install
```

## Tipos de Instalación

### Instalación Rápida

La instalación rápida está diseñada para poner tu aplicación en funcionamiento lo más rápido posible:

- **Verificación automática** de requisitos del sistema
- **Configuración de base de datos** con validación en tiempo real
- **Aplicación automática** de configuraciones de seguridad
- **Optimizaciones de producción** incluidas
- **Usuario administrador** creado automáticamente
- **Tiempo estimado**: 2-3 minutos

#### Proceso:
1. Verificación automática de requisitos
2. Configuración de base de datos MySQL
3. Instalación automática con progreso visual
4. Presentación de credenciales de administrador

### Instalación Avanzada

La instalación avanzada te da control total sobre cada aspecto:

- **Verificación manual** de requisitos con detalles
- **Configuración de base de datos** con opciones avanzadas
- **Configuración de correo electrónico** (SMTP, etc.)
- **Selección de entorno** (desarrollo/producción)
- **Opciones de optimización** personalizables
- **Control de API** (habilitar/deshabilitar)

#### Proceso:
1. Verificación detallada de requisitos
2. Configuración de base de datos
3. Configuración de migraciones
4. Configuración de entorno y correo
5. Configuración final y optimizaciones
6. Instalación con progreso detallado

## Configuración

El archivo de configuración se encuentra en `config/installer.php`:

```php
return [
    // Configuración de rutas
    'route' => [
        'prefix' => 'install',
        'middleware' => ['web'],
    ],

    // Requisitos del sistema
    'requirements' => [
        'php' => [
            'version' => '8.2.0',
            'extensions' => [
                'bcmath', 'ctype', 'fileinfo', 'json', 'mbstring',
                'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml',
                'curl', 'gd', 'zip',
            ],
        ],
        'permissions' => [
            'storage/app/' => '775',
            'storage/framework/' => '775',
            'storage/logs/' => '775',
            'bootstrap/cache/' => '775',
        ],
    ],

    // Usuario administrador por defecto
    'default_user' => [
        'name' => 'Administrador',
        'email' => 'admin@example.com',
        'password' => 'Admin123!',
    ],

    // Optimizaciones de producción
    'production_optimizations' => [
        'config_cache' => true,
        'route_cache' => true,
        'view_cache' => true,
        'optimize_autoloader' => true,
    ],

    // Configuraciones de seguridad
    'security_settings' => [
        'disable_debug' => true,
        'secure_headers' => true,
        'https_redirect' => false,
        'remove_server_header' => true,
    ],
];
```

## Comandos de Consola

### Verificar estado de instalación

```bash
php artisan installer:install
```

### Resetear instalación

```bash
php artisan installer:reset
```

## Funciones Helper

El paquete incluye funciones helper útiles:

```php
// Verificar si la aplicación está instalada
if (is_app_installed()) {
    // La aplicación está instalada
}

// Obtener configuración del instalador
$prefix = installer_config('route.prefix');

// Generar URL de assets del instalador
$cssUrl = installer_asset('css/installer.css');
```

## Personalización

### Personalizar Vistas

Publica las vistas y personalízalas según tus necesidades:

```bash
php artisan vendor:publish --tag=installer-views
```

Las vistas se copiarán a `resources/views/vendor/installer/`

### Personalizar Estilos

Publica los assets y modifica los estilos:

```bash
php artisan vendor:publish --tag=installer-assets
```

Los assets se copiarán a `public/vendor/installer/`

### Middleware Personalizado

Puedes crear tu propio middleware extendiendo el incluido:

```php
<?php

namespace App\Http\Middleware;

use dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware as BaseMiddleware;

class CustomInstallationMiddleware extends BaseMiddleware
{
    // Tu lógica personalizada
}
```

## Seguridad

### Consideraciones Importantes

1. **Eliminar después de la instalación**: Considera desinstalar el paquete después de la instalación en producción
2. **Proteger la ruta**: En producción, asegúrate de que la ruta `/install` no sea accesible
3. **Cambiar credenciales**: Cambia las credenciales del administrador después de la primera instalación
4. **Archivo de bloqueo**: El archivo `storage/installed` previene reinstalaciones accidentales

### Desinstalar el Paquete

Después de la instalación exitosa, puedes remover el paquete:

```bash
composer remove dansware03/laravel-installer
```

## Solución de Problemas

### Error: "Class not found"

Ejecuta:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Error: "Route not found"

Verifica que las rutas estén cargadas:
```bash
php artisan route:list | grep install
```

### Error: "View not found"

Publica las vistas:
```bash
php artisan vendor:publish --tag=installer-views
```

### Problemas de Permisos

Ajusta los permisos de las carpetas:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Base de Datos

Asegúrate de que:
- La base de datos existe
- Las credenciales son correctas
- El usuario tiene permisos suficientes

## Changelog

### v1.0.0
- Lanzamiento inicial
- Instalación rápida y avanzada
- Verificación completa de requisitos
- Configuración automática de producción
- Middleware de protección
- Comandos de consola

## Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea un Pull Request

## Soporte

Para reportar bugs o solicitar nuevas características:

- **Email**: dansware2003@gmail.com
- **Issues**: [GitHub Issues](https://github.com/Dansware03/laravel-installer/issues)
- **Código fuente**: [GitHub Repository](https://github.com/Dansware03/laravel-installer)

## Licencia

Este paquete es software de código abierto licenciado bajo la [MIT License](LICENSE).

## Créditos

Desarrollado por **Maiker Bravo** ([@Dansware03](https://github.com/Dansware03))

---

**¿Te gusta este paquete?** ⭐ ¡Dale una estrella en GitHub!

**¿Necesitas ayuda?** 💬 No dudes en abrir un issue o contactarnos directamente.