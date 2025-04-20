<?php

namespace Dansware\LaravelInstaller;

use Illuminate\Support\ServiceProvider;
use Dansware\LaravelInstaller\Middleware\NotInstalledMiddleware;
use Dansware\LaravelInstaller\Middleware\InstalledMiddleware;
use Dansware\LaravelInstaller\Middleware\CheckInstallationMiddleware;

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

        // Registrar helper functions
        require_once __DIR__ . '/helpers.php';
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
        $router->pushMiddlewareToGroup('web', CheckInstallationMiddleware::class);

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

        // Publicar traducciones
        //$this->publishes([
        //    __DIR__ . '/resources/lang' => resource_path('lang/vendor/installer'),
        //], 'installer-lang');

        // Todos los assets juntos
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('installer'),
            __DIR__ . '/config/installer.php' => config_path('installer.php'),
            __DIR__ . '/resources/views' => resource_path('views/installer'),
            __DIR__ . '/resources/lang' => resource_path('lang/vendor/installer'),
        ], 'installer');
    }
}
