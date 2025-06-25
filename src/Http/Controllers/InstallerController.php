<?php

namespace dansware03\laravelinstaller\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use dansware03\laravelinstaller\InstallerManager;

class InstallerController extends Controller
{
    protected $installer;

    public function __construct(InstallerManager $installer)
    {
        $this->installer = $installer;
    }

    /**
     * Mostrar página de bienvenida
     */
    public function welcome()
    {
        if ($this->installer->isInstalled()) {
            return redirect('/');
        }

        return view('installer::welcome');
    }

    /**
     * Instalación rápida - Verificar requisitos
     */
    public function quickInstall()
    {
        $requirements = $this->installer->checkRequirements();

        // Verificar si todos los requisitos se cumplen
        $allPassed = true;

        if (!$requirements['php_version']['satisfied']) {
            $allPassed = false;
        }

        foreach ($requirements['php_extensions'] as $extension => $status) {
            if (!$status['installed']) {
                $allPassed = false;
                break;
            }
        }

        foreach ($requirements['permissions'] as $path => $status) {
            if (!$status['satisfied']) {
                $allPassed = false;
                break;
            }
        }

        if (!$allPassed) {
            return view('installer::quick-requirements', compact('requirements'));
        }

        return view('installer::quick-database');
    }

    /**
     * Procesar instalación rápida
     */
    public function processQuickInstall(Request $request)
    {
        $request->validate([
            'database_host' => 'required|string',
            'database_port' => 'required|numeric',
            'database_name' => 'required|string',
            'database_username' => 'required|string',
            'database_password' => 'nullable|string',
        ]);

        // Configurar base de datos
        $dbConfig = [
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $request->database_host,
            'DB_PORT' => $request->database_port,
            'DB_DATABASE' => $request->database_name,
            'DB_USERNAME' => $request->database_username,
            'DB_PASSWORD' => $request->database_password,
        ];

        // Probar conexión
        if (
            !$this->installer->testDatabaseConnection([
                'host' => $request->database_host,
                'port' => $request->database_port,
                'database' => $request->database_name,
                'username' => $request->database_username,
                'password' => $request->database_password,
            ])
        ) {
            return back()->withErrors(['database' => 'No se pudo conectar a la base de datos. Verifique los datos ingresados.']);
        }

        // Actualizar archivo .env
        $this->installer->updateEnvironmentFile($dbConfig);

        return view('installer::quick-installing');
    }

    /**
     * Ejecutar instalación rápida (AJAX)
     */
    public function executeQuickInstall(Request $request)
    {
        try {
            // Ejecutar migraciones
            if (!$this->installer->runMigrations()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al ejecutar migraciones. Verifique la configuración de la base de datos.'
                ]);
            }

            // Aplicar configuraciones de seguridad
            if (!$this->installer->applySecuritySettings()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aplicar configuraciones de seguridad.'
                ]);
            }

            // Aplicar optimizaciones de producción
            if (!$this->installer->applyProductionOptimizations()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aplicar optimizaciones de producción.'
                ]);
            }

            // Crear usuario administrador
            $credentials = $this->installer->createAdminUser();

            // Marcar como instalado
            $this->installer->markAsInstalled();

            return response()->json([
                'success' => true,
                'credentials' => $credentials
            ]);

        } catch (\Throwable $e) { // Capturar Throwable
            \Log::error('Installer execution quick failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error durante la instalación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Instalación avanzada - Verificar requisitos
     */
    public function advancedRequirements()
    {
        $requirements = $this->installer->checkRequirements();
        return view('installer::advanced-requirements', compact('requirements'));
    }

    /**
     * Instalación avanzada - Configurar base de datos
     */
    public function advancedDatabase()
    {
        return view('installer::advanced-database');
    }

    /**
     * Procesar configuración de base de datos avanzada
     */
    public function processAdvancedDatabase(Request $request)
    {
        $request->validate([
            'database_host' => 'required|string',
            'database_port' => 'required|numeric',
            'database_name' => 'required|string',
            'database_username' => 'required|string',
            'database_password' => 'nullable|string',
        ]);

        // Probar conexión
        if (
            !$this->installer->testDatabaseConnection([
                'host' => $request->database_host,
                'port' => $request->database_port,
                'database' => $request->database_name,
                'username' => $request->database_username,
                'password' => $request->database_password,
            ])
        ) {
            return back()->withErrors(['database' => 'No se pudo conectar a la base de datos.']);
        }

        // Guardar configuración en sesión
        session([
            'installer.database' => [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->database_host,
                'DB_PORT' => $request->database_port,
                'DB_DATABASE' => $request->database_name,
                'DB_USERNAME' => $request->database_username,
                'DB_PASSWORD' => $request->database_password,
            ]
        ]);

        return redirect()->route('installer.advanced.migrations');
    }

    /**
     * Instalación avanzada - Migraciones
     */
    public function advancedMigrations()
    {
        return view('installer::advanced-migrations');
    }

    /**
     * Procesar migraciones avanzadas
     */
    public function processAdvancedMigrations(Request $request)
    {
        if ($request->run_migrations === 'yes') {
            // Actualizar .env con configuración de BD
            $this->installer->updateEnvironmentFile(session('installer.database'));

            // Ejecutar migraciones
            if (!$this->installer->runMigrations()) {
                return back()->withErrors(['migrations' => 'Error al ejecutar las migraciones.']);
            }

            session(['installer.migrations_run' => true]);
        }

        return redirect()->route('installer.advanced.environment');
    }

    /**
     * Instalación avanzada - Configuración de entorno
     */
    public function advancedEnvironment()
    {
        return view('installer::advanced-environment');
    }

    /**
     * Procesar configuración de entorno
     */
    public function processAdvancedEnvironment(Request $request)
    {
        $envConfig = [];

        if ($request->filled('app_name')) {
            $envConfig['APP_NAME'] = '"' . $request->app_name . '"';
        }

        if ($request->filled('mail_driver')) {
            $envConfig['MAIL_MAILER'] = $request->mail_driver;
            $envConfig['MAIL_HOST'] = $request->mail_host;
            $envConfig['MAIL_PORT'] = $request->mail_port;
            $envConfig['MAIL_USERNAME'] = $request->mail_username;
            $envConfig['MAIL_PASSWORD'] = $request->mail_password;
            $envConfig['MAIL_ENCRYPTION'] = $request->mail_encryption;
            // Añadir MAIL_FROM_ADDRESS y MAIL_FROM_NAME si se proporcionan
            if ($request->filled('mail_from_address')) {
                $envConfig['MAIL_FROM_ADDRESS'] = $request->mail_from_address;
            }
            // Por defecto, usar el nombre de la aplicación como MAIL_FROM_NAME si no se proporciona uno específico
            // y si se ha configurado un nombre de aplicación.
            $appName = $request->filled('app_name') ? $request->app_name : config('app.name');
            if ($appName) {
                 $envConfig['MAIL_FROM_NAME'] = '"' . $appName . '"';
            }
        }

        session(['installer.environment' => $envConfig]);

        return redirect()->route('installer.advanced.final-config');
    }

    /**
     * Instalación avanzada - Configuración final
     */
    public function advancedFinalConfig()
    {
        return view('installer::advanced-final-config');
    }

    /**
     * Procesar configuración final avanzada
     */
    public function processAdvancedFinalConfig(Request $request)
    {
        $request->validate([
            'environment_type' => 'required|in:development,production',
            'disable_api' => 'boolean',
        ]);

        session([
            'installer.final_config' => [
                'environment_type' => $request->environment_type,
                'disable_api' => $request->boolean('disable_api'),
            ]
        ]);

        return view('installer::advanced-installing');
    }

    /**
     * Ejecutar instalación avanzada (AJAX)
     */
    public function executeAdvancedInstall()
    {
        try {
            $finalConfig = session('installer.final_config');

            if (!$finalConfig || !isset($finalConfig['environment_type']) || !isset($finalConfig['disable_api'])) {
                \Log::error('Installer: Missing or incomplete final_config in session during executeAdvancedInstall.', ['session_data' => session()->all()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno: La configuración final es inválida o no se encontró en la sesión. Por favor, reinicie el proceso de instalación.'
                ]);
            }

            // Actualizar configuración de entorno
            if (session('installer.environment')) {
                $this->installer->updateEnvironmentFile(session('installer.environment'));
            }

            // Aplicar configuraciones según el tipo de entorno
            if ($finalConfig['environment_type'] === 'production') {
                $this->installer->applySecuritySettings();
                $this->installer->applyProductionOptimizations();
            } else {
                $this->installer->applyDevelopmentSettings();
            }

            // Desactivar API si se solicitó
            if ($finalConfig['disable_api']) {
                $this->installer->disableApi();
            }

            // Crear usuario administrador si se ejecutaron las migraciones
            $credentials = null;
            if (session('installer.migrations_run')) {
                $credentials = $this->installer->createAdminUser();
            }

            // Marcar como instalado
            $this->installer->markAsInstalled();

            // Limpiar sesión
            session()->forget(['installer.database', 'installer.environment', 'installer.final_config', 'installer.migrations_run']);

            return response()->json([
                'success' => true,
                'credentials' => $credentials
            ]);

        } catch (\Throwable $e) { // Capturar Throwable para errores más generales también
            // Loggear más detalles del error
            \Log::error('Installer execution advanced failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_data' => session()->all() // Loguear datos de sesión puede ayudar a diagnosticar
            ]);
            return response()->json([
                'success' => false,
                // No exponer detalles sensibles del error al cliente en producción, pero sí en desarrollo.
                // Para este paquete, dado que es una herramienta de instalación, podría ser útil mostrar más.
                'message' => 'Error durante la instalación: ' . $e->getMessage()
                             // . (config('app.debug') ? ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')' : '')
            ]);
        }
    }

    /**
     * Finalización de instalación
     */
    public function complete()
    {
        if (!$this->installer->isInstalled()) {
            return redirect()->route('installer.welcome');
        }

        return view('installer::complete');
    }
}