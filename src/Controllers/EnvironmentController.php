<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EnvironmentController extends BaseController
{
    /**
     * Muestra el formulario para configurar el entorno
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if ($redirect = $this->checkIfInstalled()) {
            return $redirect;
        }

        $steps = $this->getInstallationSteps();
        $environmentTypes = config('installer.environment.types', [
            'local' => 'Desarrollo local',
            'production' => 'Producción'
        ]);

        $cacheDrivers = config('installer.cache.drivers', [
            'file' => 'Sistema de archivos',
            'redis' => 'Redis',
            'memcached' => 'Memcached'
        ]);

        $queueDrivers = [
            'sync' => 'Sincronizado (Sin cola)',
            'database' => 'Base de datos',
            'redis' => 'Redis',
            'beanstalkd' => 'Beanstalkd'
        ];

        $mailDrivers = config('installer.mail.drivers', [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'log' => 'Log (para desarrollo)'
        ]);

        // Auto-detectar URL
        $appUrl = URL::to('/');

        // Opciones del entorno
        $apiEnabled = config('installer.environment.options.api_enabled', true);
        $allowApiConfig = config('installer.environment.options.allow_api_config', true);
        $allowCacheConfig = config('installer.environment.options.allow_cache_config', true);
        $allowQueueConfig = config('installer.environment.options.allow_queue_config', true);
        $allowMailConfig = config('installer.environment.options.allow_mail_config', true);

        return view('installer::installation.environment', compact(
            'steps',
            'environmentTypes',
            'cacheDrivers',
            'queueDrivers',
            'mailDrivers',
            'appUrl',
            'apiEnabled',
            'allowApiConfig',
            'allowCacheConfig',
            'allowQueueConfig',
            'allowMailConfig'
        ));
    }

    /**
     * Guarda la configuración del entorno
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($redirect = $this->checkIfInstalled()) {
            return $redirect;
        }

        try {
            $validationRules = [
                'app_url' => 'required|url',
                'environment' => 'required|in:' . implode(',', array_keys(config('installer.environment.types', ['local', 'production']))),
            ];

            // Validaciones condicionales
            if (config('installer.environment.options.allow_api_config', true)) {
                $validationRules['api_enabled'] = 'boolean';
            }

            if (config('installer.environment.options.allow_cache_config', true)) {
                $validationRules['cache_driver'] = 'required|in:' . implode(',', array_keys(config('installer.cache.drivers', ['file', 'redis', 'memcached'])));
            }

            if (config('installer.environment.options.allow_queue_config', true)) {
                $validationRules['queue_driver'] = 'required|in:sync,database,redis,beanstalkd';
            }

            if (config('installer.environment.options.allow_mail_config', true)) {
                $validationRules['mail_driver'] = 'required|in:' . implode(',', array_keys(config('installer.mail.drivers', ['smtp', 'sendmail', 'log'])));
            }

            $request->validate($validationRules);

            // Modificar archivo .env
            $envPath = base_path('.env');
            $envFile = file_get_contents($envPath);

            // Configuraciones básicas
            $envFile = $this->configureBasicEnvironment($envFile, $request);

            // Configuraciones específicas por tipo de entorno
            $environment = $request->input('environment');
            if ($environment === 'production') {
                $envFile = $this->configureProductionEnvironment($envFile);
            } else {
                $envFile = $this->configureDevelopmentEnvironment($envFile);
            }

            // API
            if (config('installer.environment.options.allow_api_config', true)) {
                $apiEnabled = $request->boolean('api_enabled', true);
                $envFile = set_env_value($envFile, 'API_ENABLED', $apiEnabled ? 'true' : 'false');
            }

            // Caché
            if (config('installer.environment.options.allow_cache_config', true)) {
                $cacheDriver = $request->input('cache_driver');
                $envFile = set_env_value($envFile, 'CACHE_DRIVER', $cacheDriver);

                // Configuraciones específicas para Redis o Memcached
                if ($cacheDriver === 'redis') {
                    $envFile = $this->configureRedis($envFile, $request);
                } elseif ($cacheDriver === 'memcached') {
                    $envFile = $this->configureMemcached($envFile, $request);
                }
            }
            // Cola
            if (config('installer.environment.options.allow_queue_config', true)) {
                $queueDriver = $request->input('queue_driver');
                $envFile = set_env_value($envFile, 'QUEUE_CONNECTION', $queueDriver);

                // Configuraciones específicas para colas
                if ($queueDriver === 'redis') {
                    $envFile = $this->configureRedis($envFile, $request);
                }
            }

            // Correo
            if (config('installer.environment.options.allow_mail_config', true)) {
                $mailDriver = $request->input('mail_driver');
                $envFile = set_env_value($envFile, 'MAIL_MAILER', $mailDriver);

                if ($mailDriver === 'smtp') {
                    $envFile = set_env_value($envFile, 'MAIL_HOST', $request->input('mail_host', 'smtp.mailtrap.io'));
                    $envFile = set_env_value($envFile, 'MAIL_PORT', $request->input('mail_port', '2525'));
                    $envFile = set_env_value($envFile, 'MAIL_USERNAME', $request->input('mail_username', ''));
                    $envFile = set_env_value($envFile, 'MAIL_PASSWORD', $request->input('mail_password', ''));
                    $envFile = set_env_value($envFile, 'MAIL_ENCRYPTION', $request->input('mail_encryption', 'tls'));
                }
            }

            // Escribir los cambios en el archivo .env
            file_put_contents($envPath, $envFile);

            return redirect()->route(config('installer.steps.finish.route', 'installation.finish'))
                ->with('success', 'Configuración de entorno guardada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Configurar el entorno básico en el archivo .env
     *
     * @param string $envFile
     * @param Request $request
     * @return string
     */
    private function configureBasicEnvironment($envFile, $request)
    {
        $appUrl = $request->input('app_url');
        $environment = $request->input('environment');

        $envFile = set_env_value($envFile, 'APP_URL', $appUrl);
        $envFile = set_env_value($envFile, 'APP_ENV', $environment);

        // Generar nueva clave de aplicación si no existe
        if (!preg_match('/APP_KEY=[^\s]+/', $envFile)) {
            try {
                // Intentamos generar una nueva clave
                $appKey = 'base64:' . base64_encode(random_bytes(32));
                $envFile = set_env_value($envFile, 'APP_KEY', $appKey);
            } catch (\Exception $e) {
                // Si falla, dejamos que el comando artisan key:generate lo maneje después
            }
        }

        return $envFile;
    }

    /**
     * Configurar entorno de producción
     *
     * @param string $envFile
     * @return string
     */
    private function configureProductionEnvironment($envFile)
    {
        // Configuraciones optimizadas para producción
        $envFile = set_env_value($envFile, 'APP_DEBUG', 'false');
        $envFile = set_env_value($envFile, 'LOG_LEVEL', 'error');
        $envFile = set_env_value($envFile, 'APP_MAINTENANCE_DRIVER', 'file');
        $envFile = set_env_value($envFile, 'SESSION_SECURE_COOKIE', 'true');
        $envFile = set_env_value($envFile, 'SESSION_COOKIE_HTTPONLY', 'true');
        $envFile = set_env_value($envFile, 'SESSION_SAME_SITE', 'lax');

        // Configuraciones de seguridad
        if (config('installer.environment.security.force_https', true)) {
            $envFile = set_env_value($envFile, 'FORCE_HTTPS', 'true');
        }

        // Configuraciones CORS para API
        if (config('installer.environment.security.apply_cors', true)) {
            $domain = parse_url(set_env_value($envFile, 'APP_URL'), PHP_URL_HOST);
            if ($domain) {
                $envFile = set_env_value($envFile, 'SANCTUM_STATEFUL_DOMAINS', $domain);
                $envFile = set_env_value($envFile, 'SESSION_DOMAIN', $domain);
            }
        }

        return $envFile;
    }

    /**
     * Configurar entorno de desarrollo
     *
     * @param string $envFile
     * @return string
     */
    private function configureDevelopmentEnvironment($envFile)
    {
        // Configuraciones optimizadas para desarrollo
        $envFile = set_env_value($envFile, 'APP_DEBUG', 'true');
        $envFile = set_env_value($envFile, 'LOG_LEVEL', 'debug');
        $envFile = set_env_value($envFile, 'DEBUGBAR_ENABLED', 'true');
        $envFile = set_env_value($envFile, 'APP_MAINTENANCE_DRIVER', 'file');

        // Cookies inseguras para desarrollo local
        $envFile = set_env_value($envFile, 'SESSION_SECURE_COOKIE', 'false');

        return $envFile;
    }

    /**
     * Configurar Redis
     *
     * @param string $envFile
     * @param Request $request
     * @return string
     */
    private function configureRedis($envFile, $request)
    {
        $redisHost = $request->input('redis_host', '127.0.0.1');
        $redisPassword = $request->input('redis_password', 'null');
        $redisPort = $request->input('redis_port', '6379');

        $envFile = set_env_value($envFile, 'REDIS_HOST', $redisHost);
        $envFile = set_env_value($envFile, 'REDIS_PASSWORD', $redisPassword);
        $envFile = set_env_value($envFile, 'REDIS_PORT', $redisPort);

        return $envFile;
    }

    /**
     * Configurar Memcached
     *
     * @param string $envFile
     * @param Request $request
     * @return string
     */
    private function configureMemcached($envFile, $request)
    {
        $memcachedHost = $request->input('memcached_host', '127.0.0.1');
        $memcachedPort = $request->input('memcached_port', '11211');

        $envFile = set_env_value($envFile, 'MEMCACHED_HOST', $memcachedHost);
        $envFile = set_env_value($envFile, 'MEMCACHED_PORT', $memcachedPort);

        return $envFile;
    }
}
