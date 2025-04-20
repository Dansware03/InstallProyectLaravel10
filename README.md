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

### 1. Agregar el paquete a tu proyecto Laravel

```bash
composer require Dansware03/laravel-installer
```

### 2. Publicar los assets del paquete (opcional)

```bash
php artisan vendor:publish --tag=installer-assets
```

### 3. Publicar la configuración (opcional)

```bash
php artisan vendor:publish --tag=installer-config
```

### 4. Publicar las vistas para personalizarlas (opcional)

```bash
php artisan vendor:publish --tag=installer-views
```

## Uso

Una vez instalado el paquete, simplemente accede a la URL de tu aplicación Laravel y agrega `/install` al final. Por ejemplo:

```
http://tu-aplicacion.test/install
```

El asistente te guiará a través del proceso de instalación paso a paso:

1. **Bienvenida:** Introducción al proceso de instalación
2. **Comprobación de Requisitos:** Verificación automática de que tu servidor cumple con todos los requisitos necesarios
3. **Configuración de Base de Datos:** Configuración de la conexión a la base de datos (MySQL o SQLite)
4. **Finalización:** Ajustes finales y ejecución de migraciones

## Personalización

Puedes personalizar varios aspectos del instalador editando el archivo de configuración `config/installer.php` después de publicarlo.

## Requisitos

- PHP >= 8.1
- Laravel 10.x

## Licencia

Este paquete está bajo la [Licencia MIT](LICENSE.md).