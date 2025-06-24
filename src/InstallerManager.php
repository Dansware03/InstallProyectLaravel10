<?php

namespace Dansware03\LaravelInstaller;

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
     */
    public function runMigrations(): bool
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Crear usuario administrador
     */
    public function createAdminUser(): array
    {
        $config = config('installer.default_user');

        $user = \App\Models\User::create([
            'name' => $config['name'],
            'email' => $config['email'],
            'password' => Hash::make($config['password']),
            'email_verified_at' => now(),
        ]);

        return [
            'email' => $config['email'],
            'password' => $config['password'],
        ];
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
                $content = str_replace(
                    '->withRouting(',
                    '// API disabled by installer' . PHP_EOL . '    ->withRouting(',
                    $content
                );
                File::put($bootstrapPath, $content);
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