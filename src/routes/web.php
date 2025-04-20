<?php

use Illuminate\Support\Facades\Route;
use Dansware\LaravelInstaller\Controllers\InstallationController;

Route::group([
    'prefix' => config('installer.route', 'install'),
    'as' => 'installation.',
    'middleware' => ['web'],
], function () {
    // Verificar si la aplicación ya está instalada
    Route::middleware(['installation.not-installed'])->group(function () {
        // Rutas de instalación
        Route::get('/', [InstallationController::class, 'welcome'])->name('welcome');
        Route::get('/requirements', [InstallationController::class, 'requirements'])->name('requirements');
        Route::get('/database', [InstallationController::class, 'database'])->name('database');
        Route::post('/database', [InstallationController::class, 'saveDatabase'])->name('saveDatabase');
        Route::post('/test-connection', [InstallationController::class, 'testConnection'])->name('testConnection');
        Route::get('/finish', [InstallationController::class, 'finish'])->name('finish');
        Route::post('/finish', [InstallationController::class, 'saveFinish'])->name('saveFinish');
    });

    // Redirección si ya está instalado
    Route::get('/installed', [InstallationController::class, 'installed'])->name('installed');
});