<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ruta del instalador
    |--------------------------------------------------------------------------
    |
    | Esta es la URL base donde se accederá al instalador.
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
    'logo' => 'installer/images/logo_for_dark.svg',

    /*
    |--------------------------------------------------------------------------
    | Favicon del instalador
    |--------------------------------------------------------------------------
    |
    | Ruta al favicon para las páginas del instalador.
    |
    */
    'favicon' => 'installer/images/favicon.ico',

    /*
    |--------------------------------------------------------------------------
    | Tema del instalador
    |--------------------------------------------------------------------------
    |
    | Configuración del tema para el instalador.
    |
    */
    'theme' => [
        'primary_color' => '#0ea5e9',     // Color primario (blue-500)
        'primary_color_50' => '#f0f9ff',  // Tonalidad 50 del color primario
        'primary_color_100' => '#e0f2fe', // Tonalidad 100 del color primario
        'primary_color_200' => '#bae6fd', // Tonalidad 200 del color primario
        'primary_color_300' => '#7dd3fc', // Tonalidad 300 del color primario
        'primary_color_400' => '#38bdf8', // Tonalidad 400 del color primario
        'primary_color_600' => '#0284c7', // Tonalidad 600 del color primario
        'primary_color_700' => '#0369a1', // Tonalidad 700 del color primario
        'primary_color_800' => '#075985', // Tonalidad 800 del color primario
        'primary_color_900' => '#0c4a6e', // Tonalidad 900 del color primario
        'primary_color_950' => '#082f49', // Tonalidad 950 del color primario
        'secondary_color' => '#6366F1',   // Color secundario (indigo-500)
        'background_color' => '#F3F4F6',  // Color de fondo (modo claro)
        'background_color_dark' => '#111827', // Color de fondo (modo oscuro)
        'text_color' => '#1F2937',        // Color del texto (modo claro)
        'text_color_dark' => '#F9FAFB',   // Color del texto (modo oscuro)
        'dark_mode' => false,             // Activar modo oscuro por defecto (false = detectar automáticamente)
    ],

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
            'version' => '8.2.0',
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
                'curl',
                'zip',
                'gd',
                'exif',  // Para manejo de imágenes
                'redis', // Opcional pero recomendado
                'sqlite3', // Opcional pero recomendado
                'pdo_mysql',
                'pdo_sqlite',
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
    | Opciones de base de datos
    |--------------------------------------------------------------------------
    |
    | Configuración para las opciones de base de datos en el instalador.
    |
    */
    'database' => [
        'types' => [
            'mysql' => 'MySQL',
            'sqlite' => 'SQLite',
            'pgsql' => 'PostgreSQL', // Opcional, si quieres añadir soporte
            'sqlsrv' => 'SQL Server', // Opcional, si quieres añadir soporte
        ],
        'default_ports' => [
            'mysql' => '3306',
            'pgsql' => '5432',
            'sqlsrv' => '1433',
        ],
        'test_connection' => true, // Permitir probar la conexión antes de guardar
    ],

    /*
    |--------------------------------------------------------------------------
    | Opciones de entorno
    |--------------------------------------------------------------------------
    |
    | Configuración para las opciones de entorno en el instalador.
    |
    */
    'environment' => [
        'auto_detect_url' => true, // Auto-detectar URL del sitio
        'types' => [
            'local' => 'Desarrollo local',
            'development' => 'Desarrollo',
            'staging' => 'Pruebas',
            'production' => 'Producción',
        ],
        'options' => [
            'api_enabled' => true, // Habilitar API por defecto
            'allow_api_config' => true, // Permitir configurar si la API está habilitada
            'allow_cache_config' => true, // Permitir configurar caché
            'allow_queue_config' => true, // Permitir configurar colas
            'allow_mail_config' => true, // Permitir configurar correo
        ],
        'security' => [
            'force_https' => true, // Forzar HTTPS en producción
            'session_secure' => true, // Cookies seguras en producción
            'apply_cors' => true, // Aplicar configuraciones CORS seguras
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Opciones de almacenamiento en caché
    |--------------------------------------------------------------------------
    |
    | Configuración para opciones de caché durante la instalación
    |
    */
    'cache' => [
        'drivers' => [
            'file' => 'Sistema de archivos',
            'redis' => 'Redis',
            'memcached' => 'Memcached',
            'array' => 'Array (Sin caché persistente)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Opciones de correo
    |--------------------------------------------------------------------------
    |
    | Configuración para opciones de correo durante la instalación
    |
    */
    'mail' => [
        'drivers' => [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'ses' => 'Amazon SES',
            'postmark' => 'Postmark',
            'log' => 'Log (para desarrollo)',
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
        'environment' => [
            'title' => 'Entorno',
            'icon' => 'settings',
            'view' => 'installer::installation.environment',
            'route' => 'installation.environment',
        ],
        'finish' => [
            'title' => 'Finalizar',
            'icon' => 'flag',
            'view' => 'installer::installation.finish',
            'route' => 'installation.finish',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Post-Instalación
    |--------------------------------------------------------------------------
    |
    | Configuración para acciones posteriores a la instalación
    |
    */
    'post_installation' => [
        'redirect_url' => '/', // URL a la que redirigir después de la instalación
        'run_migrations' => true, // Ejecutar migraciones por defecto
        'run_seeders' => false, // Ejecutar seeders por defecto
        'create_admin' => false, // Crear usuario administrador
    ],
];
