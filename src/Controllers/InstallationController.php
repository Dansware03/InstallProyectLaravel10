<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class InstallationController extends Controller
{
    /**
     * Muestra la pantalla de bienvenida
     */
    public function welcome()
    {
        // Verificar si ya está instalado
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        return view('installer::installation.welcome');
    }

    /**
     * Verifica los requisitos del sistema
     */
    public function requirements()
    {
        // Verificar si ya está instalado
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        $requirements = $this->checkRequirements();
        $allRequirementsMet = !in_array(false, array_column($requirements, 'result'));

        return view('installer::installation.requirements', compact('requirements', 'allRequirementsMet'));
    }

    /**
     * Formulario para configurar la base de datos
     */
    public function database()
    {
        // Verificar si ya está instalado
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        return view('installer::installation.database');
    }

    /**
     * Guarda la configuración de la base de datos
     */
    public function saveDatabase(Request $request)
    {
        // Verificar si ya está instalado
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        try {
            $request->validate([
                'database_type' => 'required|in:mysql,sqlite',
                'database_host' => 'required_if:database_type,mysql',
                'database_port' => 'required_if:database_type,mysql',
                'database_name' => 'required_if:database_type,mysql',
                'database_user' => 'required_if:database_type,mysql',
            ]);

            $databaseType = $request->input('database_type');

            // Ruta del archivo .env
            $envPath = base_path('.env');
            $envFile = file_get_contents($envPath);

            if ($databaseType === 'mysql') {
                // Para MySQL: Actualizamos o agregamos las variables y las descomentamos
                $envFile = set_env_value($envFile, 'DB_CONNECTION', 'mysql');
                $envFile = set_env_value($envFile, 'DB_HOST', $request->input('database_host'));
                $envFile = set_env_value($envFile, 'DB_PORT', $request->input('database_port'));
                $envFile = set_env_value($envFile, 'DB_DATABASE', $request->input('database_name'));
                $envFile = set_env_value($envFile, 'DB_USERNAME', $request->input('database_user'));
                $envFile = set_env_value($envFile, 'DB_PASSWORD', $request->input('database_password', ''));
            } else {
                // Para SQLite: Establecemos la conexión y comentamos las variables de MySQL
                $envFile = set_env_value($envFile, 'DB_CONNECTION', 'sqlite');
                $sqlitePath = database_path('database.sqlite');
                $envFile = comment_env_value($envFile, 'DB_HOST', '127.0.0.1');
                $envFile = comment_env_value($envFile, 'DB_PORT', '3306');
                $envFile = comment_env_value($envFile, 'DB_DATABASE', $sqlitePath);
                $envFile = comment_env_value($envFile, 'DB_USERNAME', 'root');
                $envFile = comment_env_value($envFile, 'DB_PASSWORD', '');

                // Crear el archivo SQLite si no existe
                if (!File::exists($sqlitePath)) {
                    File::put($sqlitePath, '');
                    chmod($sqlitePath, 0664);
                }
            }

            // Escribir los cambios en el archivo .env
            file_put_contents($envPath, $envFile);

            // Limpiar la caché de configuración
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return redirect()->route('installation.finish')->with('success', 'Configuración de base de datos guardada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Prueba la conexión a la base de datos
     */
    public function testConnection(Request $request)
    {
        // Validamos los parámetros recibidos
        $request->validate([
            'database_host' => 'required',
            'database_port' => 'required',
            'database_name' => 'required',
            'database_user' => 'required',
        ]);

        $host = $request->input('database_host');
        $port = $request->input('database_port');
        $database = $request->input('database_name');
        $username = $request->input('database_user');
        $password = $request->input('database_password');

        // Creamos la configuración de conexión de forma dinámica
        $config = [
            'driver' => 'mysql',
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];

        try {
            // Purge (limpiar) cualquier conexión previa y establecer una nueva conexión "testconnection"
            DB::purge('testconnection');
            config(['database.connections.testconnection' => $config]);
            // Intentamos obtener el objeto PDO de la conexión de prueba
            DB::connection('testconnection')->getPdo();

            return response()->json([
                'success' => true,
                'message' => 'Conexión exitosa a la base de datos.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Formulario para configurar URL y entorno
     */
    public function finish()
    {
        // Verificar si ya está instalado
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        return view('installer::installation.finish');
    }

    /**
     * Finaliza la instalación
     */
    public function saveFinish(Request $request)
    {
        // Verificar si ya está instalado
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        $request->validate([
            'app_url' => 'required|url',
            'environment' => 'required|in:local,production',
            'run_migrations' => 'boolean',
        ]);

        try {
            // Modificar archivo .env
            $envPath = base_path('.env');
            $envFile = file_get_contents($envPath);

            $envFile = set_env_value($envFile, 'APP_URL', $request->input('app_url'));
            $envFile = set_env_value($envFile, 'APP_ENV', $request->input('environment'));

            if ($request->input('environment') === 'production') {
                $envFile = set_env_value($envFile, 'APP_DEBUG', 'false');

                // Agregar configuraciones de seguridad adicionales para producción
                if (!preg_match('/SESSION_SECURE_COOKIE=.*/', $envFile)) {
                    $envFile .= "\nSESSION_SECURE_COOKIE=true";
                } else {
                    $envFile = set_env_value($envFile, 'SESSION_SECURE_COOKIE', 'true');
                }

                if (!preg_match('/SANCTUM_STATEFUL_DOMAINS=.*/', $envFile)) {
                    $domain = parse_url($request->input('app_url'), PHP_URL_HOST);
                    $envFile .= "\nSANCTUM_STATEFUL_DOMAINS={$domain}";
                }
            } else {
                $envFile = set_env_value($envFile, 'APP_DEBUG', 'true');
            }

            file_put_contents($envPath, $envFile);

            // Ejecutar migraciones si se solicitó
            if ($request->input('run_migrations', false)) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // Crear archivo .installed para indicar que la instalación está completa
            try {
                File::put(storage_path('.installed'), date('Y-m-d H:i:s'));
                chmod(storage_path('.installed'), 0644); // Asegurar permisos adecuados
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'No se pudo crear el archivo de instalación: ' . $e->getMessage());
            }

            // Limpiar caché
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect('/')->with('success', 'Instalación completada con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al finalizar la instalación: ' . $e->getMessage());
        }
    }

    /**
     * Página de instalación completada
     */
    public function installed()
    {
        return view('installer::installation.installed');
    }

    /**
     * Verifica los requisitos del sistema
     */
    private function checkRequirements()
    {
        $requirements = [
            'php_version' => [
                'name' => 'Versión de PHP >= 8.1',
                'result' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'current' => PHP_VERSION
            ],
            'openssl' => [
                'name' => 'OpenSSL PHP Extension',
                'result' => extension_loaded('openssl'),
                'current' => extension_loaded('openssl') ? 'Instalado' : 'No instalado'
            ],
            'pdo' => [
                'name' => 'PDO PHP Extension',
                'result' => extension_loaded('pdo'),
                'current' => extension_loaded('pdo') ? 'Instalado' : 'No instalado'
            ],
            'mbstring' => [
                'name' => 'Mbstring PHP Extension',
                'result' => extension_loaded('mbstring'),
                'current' => extension_loaded('mbstring') ? 'Instalado' : 'No instalado'
            ],
            'tokenizer' => [
                'name' => 'Tokenizer PHP Extension',
                'result' => extension_loaded('tokenizer'),
                'current' => extension_loaded('tokenizer') ? 'Instalado' : 'No instalado'
            ],
            'xml' => [
                'name' => 'XML PHP Extension',
                'result' => extension_loaded('xml'),
                'current' => extension_loaded('xml') ? 'Instalado' : 'No instalado'
            ],
            'ctype' => [
                'name' => 'Ctype PHP Extension',
                'result' => extension_loaded('ctype'),
                'current' => extension_loaded('ctype') ? 'Instalado' : 'No instalado'
            ],
            'json' => [
                'name' => 'JSON PHP Extension',
                'result' => extension_loaded('json'),
                'current' => extension_loaded('json') ? 'Instalado' : 'No instalado'
            ],
            'bcmath' => [
                'name' => 'BCMath PHP Extension',
                'result' => extension_loaded('bcmath'),
                'current' => extension_loaded('bcmath') ? 'Instalado' : 'No instalado'
            ],
            'fileinfo' => [
                'name' => 'Fileinfo PHP Extension',
                'result' => extension_loaded('fileinfo'),
                'current' => extension_loaded('fileinfo') ? 'Instalado' : 'No instalado'
            ],
            'writable_env' => [
                'name' => 'Archivo .env con permisos de escritura',
                'result' => is_writable(base_path('.env')),
                'current' => is_writable(base_path('.env')) ? 'Escribible' : 'No escribible'
            ],
            'writable_storage' => [
                'name' => 'Directorio storage con permisos de escritura',
                'result' => is_writable(storage_path()),
                'current' => is_writable(storage_path()) ? 'Escribible' : 'No escribible'
            ],
            'server_software' => [
                'name' => 'Software de servidor',
                'result' => true,
                'current' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'
            ]
        ];

        return $requirements;
    }
}
