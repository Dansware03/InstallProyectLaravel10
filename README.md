# Laravel Installer Package

Un asistente de instalación interactivo y visualmente atractivo para aplicaciones [Laravel 10](w). Facilita la configuración inicial del proyecto con opciones de instalación rápida y avanzada, guiando al usuario paso a paso con una interfaz moderna y amigable.

[![Última Versión Estable](https://img.shields.io/packagist/v/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Total de Descargas](https://img.shields.io/packagist/dt/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)
[![Licencia](https://img.shields.io/packagist/l/dansware03/laravelinstaller.svg?style=flat-square)](https://packagist.org/packages/dansware03/laravelinstaller)

---

## 🚩 Características Principales

* ✨ **Interfaz Moderna y Amigable**
  UI clara con diseño lateral que guía paso a paso el proceso de instalación.
* 🚀 **Instalación Rápida**
  Configuración automatizada con ajustes predeterminados. Ideal para producción.
* 🛠️ **Instalación Avanzada**
  Control total sobre base de datos, entorno, optimizaciones y más.
* ✅ **Verificación de Requisitos**
  PHP, extensiones necesarias y permisos verificados automáticamente.
* 🔒 **Seguridad Integrada**
  Middleware de protección, archivo `storage/installed`, y ajustes seguros.
* ⚙️ **Configuración Detallada**
  Soporte para SMTP, entorno (dev/prod), migraciones, caché, y más.
* 👤 **Usuario Administrador Automático**
  Se crea si se ejecutan migraciones durante la instalación.
* 💻 **Comandos Artisan**
  Para lanzar o resetear el proceso de instalación.
* 🎨 **Totalmente Personalizable**
  Publica vistas, assets y configuración según tus necesidades.

---

## 📦 Requisitos

* PHP ^8.2
* Laravel 10.x
* Extensiones PHP requeridas:
  `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `curl`, `gd`, `zip`
* Permisos de escritura en:

  * `storage/app/`
  * `storage/framework/`
  * `storage/logs/`
  * `bootstrap/cache/`

---

## ⚙️ Instalación

### 1. Instala el paquete vía Composer

```bash
composer require dansware03/laravelinstaller
```

### 2. Publica archivos del paquete

#### a. Configuración (obligatorio)

```bash
php artisan vendor:publish --tag=installer-config
```

#### b. Vistas (opcional)

```bash
php artisan vendor:publish --tag=installer-views
```

#### c. Assets (opcional)

```bash
php artisan vendor:publish --tag=installer-assets
```

### 3. Verifica el archivo `.env`

Si no existe, crea uno:

```bash
cp .env.example .env
```

### 4. Middleware de Protección

Agrega en `app/Http/Kernel.php` al grupo `web`:

```php
\dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware::class,
```

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

## 🔐 Seguridad

* `storage/installed`: bloquea reinstalación
* Middleware de protección: previene acceso sin instalación
* **Importante**: cambia las credenciales temporales del administrador
* Recomendación para producción:

  * Comentar la línea `loadRoutesFrom(...)` en `LaravelInstallerServiceProvider`
  * O configurar reglas del servidor (Nginx/Apache)
  * O desinstalar el paquete completamente

---

## 🧩 Personalización

* `config/installer.php`: cambia rutas, requisitos, permisos, credenciales
* `resources/views/vendor/installer/`: personaliza interfaz
* `public/vendor/installer/`: ajusta CSS/JS personalizados
* Traducciones: publica vistas y usa funciones de localización como `__('msg')`

---

## 🧹 Desinstalación

1. Elimina el middleware de `Kernel.php`
2. Borra:

   * `config/installer.php`
   * `resources/views/vendor/installer/`
   * `public/vendor/installer/`
3. Borra `storage/installed` (si existe)
4. Ejecuta:

   ```bash
   composer remove dansware03/laravelinstaller
   ```
5. Limpia la caché:

   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

## 🛠️ Solución de Problemas

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