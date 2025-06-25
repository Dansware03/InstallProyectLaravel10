<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Installer Configuration
    |--------------------------------------------------------------------------
    */

    'route' => [
        'prefix' => 'install',
        'middleware' => ['web'],
    ],

    'requirements' => [
        'php' => [
            'version' => '8.2.0',
            'extensions' => [
                'bcmath',
                'ctype',
                'fileinfo',
                'json',
                'mbstring',
                'openssl',
                'pdo',
                'pdo_mysql',
                'tokenizer',
                'xml',
                'curl',
                'gd',
                'zip',
            ],
        ],
        'permissions' => [
            'storage/app/' => '775',
            'storage/framework/' => '775',
            'storage/logs/' => '775',
            'bootstrap/cache/' => '775',
        ],
    ],

    'default_user' => [
        'name' => 'Administrador',
        'email' => 'admin@example.com',
        'password' => 'Admin123!',
    ],

    'production_optimizations' => [
        'config_cache' => true,
        'route_cache' => true,
        'view_cache' => true,
        'optimize_autoloader' => true,
        // 'remove_dev_packages' => false, // Eliminado: cubierto por optimize_autoloader que usa --no-dev
    ],

    'security_settings' => [
        'disable_debug' => true,
        // 'secure_headers' => true, // Eliminado: No implementado directamente por el instalador
        'https_redirect' => false, // El usuario debe manejar esto a nivel de servidor/aplicación
        // 'remove_server_header' => true, // Eliminado: Mejor manejado a nivel de servidor web
    ],

    'installer_lock_file' => storage_path('installed'),
];