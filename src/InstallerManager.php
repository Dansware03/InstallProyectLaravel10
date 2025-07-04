<?php

namespace dansware03\laravelinstaller;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Process;

class InstallerManager
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Verificar si la aplicación ya está instalada
     */
    public function isInstalled(): bool
    {
        return File::exists(config('installer.installer_lock_file'));
    }

    /**
     * Marcar la aplicación como instalada
     */
    public function markAsInstalled(): void
    {
        File::put(config('installer.installer_lock_file'), json_encode([
            'installed_at' => now()->toDateTimeString(),
            'version' => config('app.version', '1.0.0'),
        ]));
    }

    /**
     * Verificar requisitos del sistema
     */
    public function checkRequirements(): array
    {
        $requirements = config('installer.requirements');
        $results = [];

        // Verificar versión de PHP
        $results['php_version'] = [
            'required' => $requirements['php']['version'],
            'current' => PHP_VERSION,
            'satisfied' => version_compare(PHP_VERSION, $requirements['php']['version'], '>=')
        ];

        // Verificar extensiones de PHP
        $results['php_extensions'] = [];
        foreach ($requirements['php']['extensions'] as $extension) {
            $results['php_extensions'][$extension] = [
                'required' => true,
                'installed' => extension_loaded($extension)
            ];
        }

        // Verificar permisos de carpetas
        $results['permissions'] = [];
        foreach ($requirements['permissions'] as $path => $permission) {
            $fullPath = base_path($path);
            $results['permissions'][$path] = [
                'required' => $permission,
                'current' => $this->getPermission($fullPath),
                'satisfied' => is_writable($fullPath)
            ];
        }

        return $results;
    }

    /**
     * Verificar conexión a la base de datos
     */
    public function testDatabaseConnection(array $config): bool
    {
        try {
            config([
                'database.connections.test_connection' => [
                    'driver' => 'mysql',
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'database' => $config['database'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]
            ]);

            DB::connection('test_connection')->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Configurar variables de entorno
     */
    public function updateEnvironmentFile(array $config): bool
    {
        try {
            $envPath = base_path('.env');

            if (!File::exists($envPath)) {
                File::copy(base_path('.env.example'), $envPath);
            }

            $envContent = File::get($envPath);

            foreach ($config as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            File::put($envPath, $envContent);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Ejecutar migraciones
     * @param array|null $dbConfig La configuración de BD que se acaba de escribir en .env (opcional).
     *                             Si se proporciona, se usará para una verificación de Schema más directa.
     */
    public function runMigrations(array $dbConfig = null): bool
    {
        try {
            // Clear config cache to ensure fresh database connection details from .env are used
            Artisan::call('config:clear');
            \Log::info('Installer: Cleared config cache before running migrations.');

            // It's good practice to refresh the application's database connection
            // as .env might have just been updated.
            // Flushing and reconnecting the default connection.
            $defaultConnectionName = config('database.default');
            DB::purge($defaultConnectionName);
            DB::reconnect($defaultConnectionName);
            $dbNameBeforeMigrate = DB::connection($defaultConnectionName)->getDatabaseName();
            \Log::info('Installer: Database connection purged and reconnected. DB name before migrate: ' . $dbNameBeforeMigrate . ' on connection: ' . $defaultConnectionName);

            $exitCode = Artisan::call('migrate', [
                '--force' => true,
            ]);

            $dbNameAfterMigrate = DB::connection($defaultConnectionName)->getDatabaseName(); // Puede fallar si la BD no existe
            \Log::info('Installer: Artisan migrate command finished with exit code ' . $exitCode . '. DB name after migrate: ' . $dbNameAfterMigrate . ' on connection: ' . $defaultConnectionName);

            if ($exitCode === 0) {
                \Log::info('Installer: Artisan migrate command reported success (exit code 0). Verifying schema...');

                $usersTableExists = false;
                $verificationConnectionName = $defaultConnectionName;

                if ($dbConfig && isset($dbConfig['DB_HOST'], $dbConfig['DB_DATABASE'], $dbConfig['DB_USERNAME'])) {
                    // Si se pasó $dbConfig (flujo avanzado), usarlo para una conexión de verificación temporal
                    // Esto asegura que estamos verificando contra la BD que acabamos de configurar.
                    $tempConnectionName = 'installer_verify_migration';
                    config(["database.connections.{$tempConnectionName}" => [
                        'driver' => $dbConfig['DB_CONNECTION'] ?? 'mysql',
                        'host' => $dbConfig['DB_HOST'],
                        'port' => $dbConfig['DB_PORT'] ?? '3306',
                        'database' => $dbConfig['DB_DATABASE'],
                        'username' => $dbConfig['DB_USERNAME'],
                        'password' => $dbConfig['DB_PASSWORD'] ?? '',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => '',
                        'strict' => true,
                        'engine' => null,
                    ]]);
                    $verificationConnectionName = $tempConnectionName;
                    \Log::info('Installer: Using temporary connection for Schema check: ' . $tempConnectionName . ' with DB: ' . $dbConfig['DB_DATABASE']);
                } else {
                    \Log::info('Installer: Using default connection for Schema check: ' . $defaultConnectionName);
                }

                try {
                    $usersTableExists = \Illuminate\Support\Facades\Schema::connection($verificationConnectionName)->hasTable('users');
                } catch (\Exception $schemaException) {
                    \Log::error('Installer: Exception during Schema::hasTable check on connection ' . $verificationConnectionName, [
                        'message' => $schemaException->getMessage(),
                        'db_name' => $dbConfig ? $dbConfig['DB_DATABASE'] : DB::connection($defaultConnectionName)->getDatabaseName(),
                    ]);
                    // Si la conexión de Schema falla, no podemos confirmar, así que asumimos que la tabla no existe.
                    $usersTableExists = false;
                }

                if ($usersTableExists) {
                    \Log::info('Installer: Schema::hasTable("users") confirmed table exists after migration on DB: ' . ($dbConfig ? $dbConfig['DB_DATABASE'] : $dbNameAfterMigrate));
                    return true;
                } else {
                    \Log::error('Installer: Schema::hasTable("users") reports table DOES NOT exist after Artisan migrate success (exit code 0) on DB: ' . $dbNameAfterMigrate . '. This is highly unexpected.');
                    return false;
                }
            } else {
                \Log::error('Installer: Artisan migrate command failed.', [
                    'exit_code' => $exitCode,
                    'db_name_at_failure' => $dbNameAfterMigrate
                ]);
                return false;
            }
        } catch (\Throwable $e) {
            \Log::error('Installer: Exception during runMigrations.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Crear usuario administrador
     */
    public function createAdminUser(): array
    {
        // Forzar reconexión y loguear DB name justo antes de la operación de Eloquent
        try {
            DB::purge(config('database.default'));
            DB::reconnect(config('database.default'));
            $dbName = DB::connection(config('database.default'))->getDatabaseName();
            \Log::info('Installer: DB purged and reconnected before createAdminUser. Current DB: ' . $dbName);
        } catch (\Throwable $e) {
            \Log::error('Installer: Failed to purge/reconnect DB in createAdminUser.', ['error' => $e->getMessage()]);
            // Continuar de todas formas, ya que la conexión podría estar bien, pero loguear el intento fallido.
        }

        $config = config('installer.default_user');

        // Loguear antes de intentar crear el usuario
        \Log::info('Installer: Attempting to create admin user.', ['email' => $config['email'], 'name' => $config['name']]);

        try {
            $user = \App\Models\User::create([
                'name' => $config['name'],
                'email' => $config['email'],
                'password' => Hash::make($config['password']),
                'email_verified_at' => now(), // Asegurar que el usuario esté verificado
            ]);

            \Log::info('Installer: Admin user created successfully.', ['user_id' => $user->id]);

            return [
                'email' => $config['email'],
                'password' => $config['password'], // Devolver la contraseña en texto plano para mostrarla al usuario
            ];
        } catch (\Throwable $e) {
            \Log::error('Installer: Failed to create admin user.', [
                'error' => $e->getMessage(),
                'db_name' => DB::connection(config('database.default'))->getDatabaseName(), // Loguear sobre qué BD falló
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Re-lanzar la excepción para que sea manejada por el bloque try/catch del controlador
            // y se devuelva una respuesta JSON de error al frontend.
            throw $e;
        }
    }

    /**
     * Aplicar optimizaciones para producción
     */
    public function applyProductionOptimizations(): bool
    {
        try {
            $optimizations = config('installer.production_optimizations');

            if ($optimizations['config_cache']) {
                Artisan::call('config:cache');
            }

            if ($optimizations['route_cache']) {
                Artisan::call('route:cache');
            }

            if ($optimizations['view_cache']) {
                Artisan::call('view:cache');
            }

            if ($optimizations['optimize_autoloader']) {
                $process = new Process(['composer', 'install', '--optimize-autoloader', '--no-dev']);
                $process->setWorkingDirectory(base_path());
                $process->run();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Aplicar configuraciones de seguridad
     */
    public function applySecuritySettings(): bool
    {
        try {
            $security = config('installer.security_settings');
            $envUpdates = [];

            if ($security['disable_debug']) {
                $envUpdates['APP_DEBUG'] = 'false';
                $envUpdates['APP_ENV'] = 'production';
            }

            // Generar APP_KEY si no existe
            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Detectar URL automáticamente
            $appUrl = $this->detectAppUrl();
            if ($appUrl) {
                $envUpdates['APP_URL'] = $appUrl;
            }

            if (!empty($envUpdates)) {
                $this->updateEnvironmentFile($envUpdates);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Configurar para desarrollo
     */
    public function applyDevelopmentSettings(): bool
    {
        try {
            $envUpdates = [
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
                'LOG_LEVEL' => 'debug',
            ];

            $this->updateEnvironmentFile($envUpdates);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Desactivar API de Laravel
     */
    public function disableApi(): bool
    {
        try {
            $routesPath = base_path('routes/api.php');
            if (File::exists($routesPath)) {
                File::move($routesPath, $routesPath . '.disabled');
            }

            // Comentar la línea en bootstrap/app.php si existe
            $bootstrapPath = base_path('bootstrap/app.php');
            if (File::exists($bootstrapPath)) {
                $content = File::get($bootstrapPath);
                $originalRoutingLine = '->withRouting(';
                $commentedRoutingLine = '// API disabled by installer' . PHP_EOL . '    ->withRouting('; // Mantener indentación original

                // Verificar si ya está comentado por este instalador
                if (strpos($content, $commentedRoutingLine) === false) {
                    // Verificar si la línea original existe y no está ya comentada de otra forma
                    if (strpos($content, $originalRoutingLine) !== false && strpos($content, '//' . $originalRoutingLine) === false) {
                        $content = str_replace(
                            $originalRoutingLine,
                            $commentedRoutingLine,
                            $content
                        );
                        File::put($bootstrapPath, $content);
                    } elseif (strpos($content, $originalRoutingLine) === false && strpos($content, '//' . $originalRoutingLine) !== false) {
                        // La línea original no se encontró, pero una versión comentada sí.
                        // Podría ser comentada manualmente o por otro proceso. No hacer nada para evitar conflictos.
                        \Log::info('Installer: API routing line in bootstrap/app.php seems to be already commented. Skipping modification.');
                    }
                } else {
                    \Log::info('Installer: API routing line in bootstrap/app.php already commented by this installer. Skipping.');
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Detectar URL de la aplicación automáticamente
     */
    private function detectAppUrl(): ?string
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            return "{$protocol}://{$host}";
        }
        return null;
    }

    /**
     * Obtener permisos de un archivo/directorio
     */
    private function getPermission(string $path): string
    {
        if (!File::exists($path)) {
            return 'N/A';
        }
        return substr(sprintf('%o', fileperms($path)), -3);
    }
}