<?php

namespace dansware03\laravelinstaller;

use Illuminate\Support\ServiceProvider;
use dansware03\laravelinstaller\Http\Middleware\InstallationMiddleware;
use dansware03\laravelinstaller\Console\Commands;

class LaravelInstallerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/installer.php', 'installer'
        );

        $this->app->singleton('installer', function ($app) {
            return new InstallerManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publicar configuración
        $this->publishes([
            __DIR__.'/../config/installer.php' => config_path('installer.php'),
        ], 'installer-config');

        // Publicar vistas
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/installer'),
        ], 'installer-views');

        // Publicar assets
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/installer'),
        ], 'installer-assets');

        // Cargar vistas
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'installer');

        // Cargar rutas
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Registrar middleware
        $this->app['router']->aliasMiddleware('installer', InstallationMiddleware::class);

        // Comandos de consola
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\InstallCommand::class,
                Console\Commands\ResetInstallCommand::class,
            ]);
        }
    }
}