<?php

use Illuminate\Support\Facades\Route;
use dansware03\laravelinstaller\Http\Controllers\InstallerController;

Route::group([
    'prefix' => config('installer.route.prefix', 'install'),
    'middleware' => config('installer.route.middleware', ['web']),
    'as' => 'installer.',
], function () {
    
    // Página de bienvenida
    Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
    
    // Instalación rápida
    Route::get('/quick', [InstallerController::class, 'quickInstall'])->name('quick');
    Route::post('/quick', [InstallerController::class, 'processQuickInstall'])->name('quick.process');
    Route::post('/quick/execute', [InstallerController::class, 'executeQuickInstall'])->name('quick.execute');
    
    // Instalación avanzada
    Route::prefix('advanced')->as('advanced.')->group(function () {
        Route::get('/requirements', [InstallerController::class, 'advancedRequirements'])->name('requirements');
        
        Route::get('/database', [InstallerController::class, 'advancedDatabase'])->name('database');
        Route::post('/database', [InstallerController::class, 'processAdvancedDatabase'])->name('database.process');
        
        Route::get('/migrations', [InstallerController::class, 'advancedMigrations'])->name('migrations');
        Route::post('/migrations', [InstallerController::class, 'processAdvancedMigrations'])->name('migrations.process');
        
        Route::get('/environment', [InstallerController::class, 'advancedEnvironment'])->name('environment');
        Route::post('/environment', [InstallerController::class, 'processAdvancedEnvironment'])->name('environment.process');
        
        Route::get('/final-config', [InstallerController::class, 'advancedFinalConfig'])->name('final-config');
        Route::post('/final-config', [InstallerController::class, 'processAdvancedFinalConfig'])->name('final-config.process');
        
        Route::post('/execute', [InstallerController::class, 'executeAdvancedInstall'])->name('execute');
    });
    
    // Completar instalación
    Route::get('/complete', [InstallerController::class, 'complete'])->name('complete');
});