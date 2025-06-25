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
            $dbConfig = session('installer.database');
            if (!$dbConfig) {
                 \Log::error('Installer: Database configuration not found in session during processAdvancedMigrations.');
                 return back()->withErrors(['migrations' => 'Error crítico: Configuración de base de datos no encontrada. Vuelva al paso anterior y reintente.']);
            }
            // Actualizar .env con configuración de BD
            $this->installer->updateEnvironmentFile($dbConfig);

            // Ejecutar migraciones
            if (!$this->installer->runMigrations()) { // runMigrations() ahora verifica Schema::hasTable('users')
                return back()->withErrors(['migrations' => 'Error al ejecutar las migraciones o la tabla de usuarios no se creó correctamente. Verifique los logs del servidor para más detalles.']);
            }

            session(['installer.migrations_run' => true]);
        } else {
            // Asegurarse de que si el usuario desmarca la opción, la sesión lo refleje.
            session(['installer.migrations_run' => false]);
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
            $envConfig['APP_NAME'] = $this->prepareEnvValue($request->app_name);
        }

        // Preparar MAIL_FROM_NAME primero, ya que puede depender de app_name
        $mailFromName = null;
        if ($request->filled('app_name')) {
            $mailFromName = $request->app_name;
        } elseif (config('app.name') && config('app.name') !== 'Laravel') {
            // Usar el config('app.name') actual solo si es significativo (no el default 'Laravel')
            // y no se proporcionó un app_name en el request.
            $mailFromName = config('app.name');
        }
        // Si el usuario provee un MAIL_FROM_NAME específico en el formulario (no implementado actualmente pero por si acaso)
        // if ($request->filled('mail_from_name_input')) {
        //    $mailFromName = $request->mail_from_name_input;
        // }


        if ($request->filled('mail_driver')) {
            $envConfig['MAIL_MAILER'] = $this->prepareEnvValue($request->mail_driver);
            $envConfig['MAIL_HOST'] = $this->prepareEnvValue($request->mail_host);
            $envConfig['MAIL_PORT'] = $this->prepareEnvValue($request->mail_port);
            $envConfig['MAIL_USERNAME'] = $this->prepareEnvValue($request->mail_username);
            $envConfig['MAIL_PASSWORD'] = $this->prepareEnvValue($request->mail_password); // Las contraseñas pueden tener caracteres especiales
            $envConfig['MAIL_ENCRYPTION'] = $this->prepareEnvValue($request->mail_encryption);

            if ($request->filled('mail_from_address')) {
                $envConfig['MAIL_FROM_ADDRESS'] = $this->prepareEnvValue($request->mail_from_address);
            }

            // Usar el $mailFromName determinado anteriormente
            if ($mailFromName) {
                 $envConfig['MAIL_FROM_NAME'] = $this->prepareEnvValue($mailFromName);
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

    /**
     * Prepara un valor para ser escrito en el archivo .env, añadiendo comillas si es necesario.
     */
    private function prepareEnvValue($value): string
    {
        // Si el valor está vacío o es nulo, devolver una cadena vacía para el .env
        // (esto efectivamente "borrará" la variable si se escribe como KEY=)
        // o se puede optar por no añadir la clave al array $envConfig si el valor es nulo/vacío.
        // Por ahora, si es null, devolvemos string vacío. Si es string vacío, se queda así.
        if (is_null($value)) {
            return '';
        }

        $value = (string) $value; // Asegurar que sea string

        // Si el valor ya está correctamente entrecomillado (simple o doble)
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            return $value;
        }

        // Si el valor contiene espacios, $, #, =, o comillas (simples o dobles) en su interior,
        // o si está vacío y queremos representarlo como KEY="", entonces lo encerramos entre comillas dobles.
        // Un valor vacío sin comillas (KEY=) es válido en .env para significar "nulo" o vacío.
        // Si queremos un string vacío literal, es KEY="".
        // Aquí, si $value es un string vacío después del casteo, lo dejamos como está.
        // Solo añadimos comillas si hay caracteres problemáticos o espacios.
        if (preg_match('/\\s|\\$|#|=|"|\'/', $value)) {
            // Escapar comillas dobles internas y backslashes antes de encerrar
            $value = str_replace('\\', '\\\\', $value);
            $value = str_replace('"', '\\"', $value);
            return '"' . $value . '"';
        }

        // Para valores simples sin espacios ni caracteres problemáticos, no se requieren comillas.
        return $value;
    }
}