<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ruta del instalador
    |--------------------------------------------------------------------------
    |
    | Esta es la URL donde se accederá al instalador.
    |
    */
    'route' => 'install',

    /*
    |--------------------------------------------------------------------------
    | Título del instalador
    |--------------------------------------------------------------------------
    |
    | Título que aparecerá en la parte superior de las páginas del instalador.
    |
    */
    'title' => 'Instalador de Aplicación',

    /*
    |--------------------------------------------------------------------------
    | Logo del instalador
    |--------------------------------------------------------------------------
    |
    | Ruta al logo que aparecerá en las páginas del instalador.
    |
    */
    'logo' => '/vendor/installer/images/logo.svg',

    /*
    |--------------------------------------------------------------------------
    | Requisitos del servidor
    |--------------------------------------------------------------------------
    |
    | Lista de requisitos del servidor y PHP que deben cumplirse.
    |
    */
    'requirements' => [
        'php' => [
            'version' => '8.1.0',
            'extensions' => [
                'openssl',
                'pdo',
                'mbstring',
                'tokenizer',
                'xml',
                'ctype',
                'json',
                'bcmath',
                'fileinfo',
            ],
        ],
        'apache' => [
            'mod_rewrite',
        ],
        'permissions' => [
            'storage/framework/' => '775',
            'storage/logs/' => '775',
            'bootstrap/cache/' => '775',
            '.env' => '664',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pasos del instalador
    |--------------------------------------------------------------------------
    |
    | Lista de pasos que se mostrarán en el instalador.
    |
    */
    'steps' => [
        'welcome' => [
            'title' => 'Bienvenido',
            'icon' => 'home',
            'view' => 'installer::installation.welcome',
            'route' => 'installation.welcome',
        ],
        'requirements' => [
            'title' => 'Requisitos',
            'icon' => 'check-circle',
            'view' => 'installer::installation.requirements',
            'route' => 'installation.requirements',
        ],
        'database' => [
            'title' => 'Base de Datos',
            'icon' => 'database',
            'view' => 'installer::installation.database',
            'route' => 'installation.database',
        ],
        'finish' => [
            'title' => 'Finalizar',
            'icon' => 'flag',
            'view' => 'installer::installation.finish',
            'route' => 'installation.finish',
        ],
    ],
];