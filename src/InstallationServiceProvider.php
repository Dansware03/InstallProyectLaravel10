<?php

namespace Dansware\LaravelInstaller;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
            __DIR__.'/config/installer.php', 'installer'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Carga rutas
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Carga vistas
        $this->loadViewsFrom(__DIR__.'/resources/views', 'installer');

        // Publicar assets
        $this->publishes([
            __DIR__.'/resources/assets' => public_path('vendor/installer'),
        ], 'installer-assets');

        // Publicar configuración
        $this->publishes([
            __DIR__.'/config/installer.php' => config_path('installer.php'),
        ], 'installer-config');

        // Publicar vistas para que el usuario pueda personalizarlas
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/installer'),
        ], 'installer-views');

        // Publicar archivos de migración
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'installer-migrations');
    }
}