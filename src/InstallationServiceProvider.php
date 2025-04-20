<?php

namespace Dansware\LaravelInstaller;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Dansware\LaravelInstaller\Middleware\NotInstalledMiddleware;
use Dansware\LaravelInstaller\Middleware\InstalledMiddleware;

class InstallationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/installer.php',
            'installer'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Registrar middlewares
        $router = $this->app['router'];
        $router->aliasMiddleware('installation.not-installed', NotInstalledMiddleware::class);
        $router->aliasMiddleware('installation.installed', InstalledMiddleware::class);

        // Carga rutas
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Carga vistas
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'installer');

        // Publicar assets
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('installer'),
        ], 'installer-assets');

        // Publicar configuración
        $this->publishes([
            __DIR__ . '/config/installer.php' => config_path('installer.php'),
        ], 'installer-config');

        // Publicar vistas para que el usuario pueda personalizarlas
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/installer'),
        ], 'installer-views');

        // Publicar archivos de migración
        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations'),
        ], 'installer-migrations');

        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('installer'),
            __DIR__ . '/config/installer.php' => config_path('installer.php'),
            __DIR__ . '/resources/views' => resource_path('views/installer'),
            //__DIR__ . '/database/migrations' => database_path('migrations'),
        ], 'installer');
    }
}
