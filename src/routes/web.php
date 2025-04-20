<?php

use Illuminate\Support\Facades\Route;
use Dansware\LaravelInstaller\Controllers\WelcomeController;
use Dansware\LaravelInstaller\Controllers\RequirementsController;
use Dansware\LaravelInstaller\Controllers\DatabaseController;
use Dansware\LaravelInstaller\Controllers\EnvironmentController;
use Dansware\LaravelInstaller\Controllers\FinishController;

Route::group(['prefix' => config('installer.route', 'install'), 'as' => 'installation.', 'middleware' => ['web', 'installation.not-installed']], function () {
    // Bienvenida
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

    // Requisitos
    Route::get('/requirements', [RequirementsController::class, 'index'])->name('requirements');

    // Base de datos
    Route::get('/database', [DatabaseController::class, 'index'])->name('database');
    Route::post('/database', [DatabaseController::class, 'store'])->name('database.store');
    Route::post('/database/test-connection', [DatabaseController::class, 'testConnection'])->name('database.test');

    // Entorno
    Route::get('/environment', [EnvironmentController::class, 'index'])->name('environment');
    Route::post('/environment', [EnvironmentController::class, 'store'])->name('environment.store');

    // Finalizar
    Route::get('/finish', [FinishController::class, 'index'])->name('finish');
    Route::post('/finish', [FinishController::class, 'store'])->name('finish.store');
});

// Ruta para cuando ya está instalado
Route::get('/installed', [FinishController::class, 'installed'])
    ->name('installation.installed')
    ->middleware(['web', 'installation.installed']);
