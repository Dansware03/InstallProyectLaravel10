<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends BaseController
{
    /**
     * Muestra el formulario para configurar la base de datos
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if ($redirect = $this->checkIfInstalled()) {
            return $redirect;
        }

        $databaseTypes = config('installer.database.types', [
            'mysql' => 'MySQL',
            'sqlite' => 'SQLite'
        ]);

        $defaultPorts = config('installer.database.default_ports', [
            'mysql' => '3306',
            'pgsql' => '5432',
            'sqlsrv' => '1433',
        ]);

        $steps = $this->getInstallationSteps();

        return view('installer::installation.database', compact('databaseTypes', 'defaultPorts', 'steps'));
    }

    /**
     * Guarda la configuración de la base de datos
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
            $this->validateDatabaseConfig($request);

            $databaseType = $request->input('database_type');
            $envPath = base_path('.env');
            $envFile = file_get_contents($envPath);

            // Configurar según el tipo de base de datos
            switch ($databaseType) {
                case 'mysql':
                case 'pgsql':
                case 'sqlsrv':
                    $envFile = $this->configureRelationalDatabase($envFile, $databaseType, $request);
                    break;

                case 'sqlite':
                    $envFile = $this->configureSqliteDatabase($envFile);
                    break;
            }

            // Escribir los cambios en el archivo .env
            file_put_contents($envPath, $envFile);

            // Limpiar la caché de configuración
            $this->clearConfigCache();

            return redirect()->route(config('installer.steps.environment.route', 'installation.environment'))
                    ->with('success', 'Configuración de base de datos guardada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar la configuración: ' . $e->getMessage())
                                    ->withInput();
        }
    }

    /**
     * Prueba la conexión a la base de datos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection(Request $request)
    {
        $databaseType = $request->input('database_type');

        // Si es SQLite, solo verificamos si podemos crear el archivo
        if ($databaseType === 'sqlite') {
            try {
                $sqlitePath = database_path('database.sqlite');

                // Verificar si el directorio existe y es escribible
                if (!File::exists(database_path())) {
                    File::makeDirectory(database_path(), 0755, true);
                }

                // Intentar escribir en el archivo (o crearlo si no existe)
                if (!File::exists($sqlitePath)) {
                    File::put($sqlitePath, '');
                    chmod($sqlitePath, 0664);
                } else {
                    // Verificar permisos de escritura
                    if (!is_writable($sqlitePath)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No se puede escribir en el archivo SQLite. Verifique los permisos.'
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Archivo SQLite verificado correctamente.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al verificar SQLite: ' . $e->getMessage()
                ]);
            }
        }

        // Para bases de datos relacionales (MySQL, PostgreSQL, SQL Server)
        try {
            $this->validateConnectionRequest($request);

            $host = $request->input('database_host');
            $port = $request->input('database_port');
            $database = $request->input('database_name');
            $username = $request->input('database_user');
            $password = $request->input('database_password');

            // Configuración base según el tipo de BD
            $config = [
                'driver' => $databaseType,
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

            // Configuraciones específicas por tipo de BD
            if ($databaseType === 'pgsql') {
                $config['charset'] = 'utf8';
                $config['schema'] = 'public';
                unset($config['collation']);
                unset($config['engine']);
            } elseif ($databaseType === 'sqlsrv') {
                unset($config['charset']);
                unset($config['collation']);
                unset($config['engine']);
            }

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
     * Validar la configuración de la base de datos
     *
     * @param Request $request
     * @return void
     */
    private function validateDatabaseConfig(Request $request)
    {
        $rules = [
            'database_type' => 'required|in:' . implode(',', array_keys(config('installer.database.types', ['mysql', 'sqlite']))),
        ];

        // Reglas para bases de datos relacionales
        if ($request->input('database_type') !== 'sqlite') {
            $rules = array_merge($rules, [
                'database_host' => 'required',
                'database_port' => 'required|numeric',
                'database_name' => 'required',
                'database_user' => 'required',
            ]);
        }

        $request->validate($rules);
    }

    /**
     * Validar la solicitud de prueba de conexión
     *
     * @param Request $request
     * @return void
     */
    private function validateConnectionRequest(Request $request)
    {
        $request->validate([
            'database_type' => 'required|in:' . implode(',', array_keys(config('installer.database.types', ['mysql', 'sqlite']))),
            'database_host' => 'required',
            'database_port' => 'required|numeric',
            'database_name' => 'required',
            'database_user' => 'required',
        ]);
    }

    /**
     * Configurar una base de datos relacional en el archivo .env
     *
     * @param string $envFile
     * @param string $databaseType
     * @param Request $request
     * @return string
     */
    private function configureRelationalDatabase($envFile, $databaseType, $request)
    {
        $envFile = set_env_value($envFile, 'DB_CONNECTION', $databaseType);
        $envFile = set_env_value($envFile, 'DB_HOST', $request->input('database_host'));
        $envFile = set_env_value($envFile, 'DB_PORT', $request->input('database_port'));
        $envFile = set_env_value($envFile, 'DB_DATABASE', $request->input('database_name'));
        $envFile = set_env_value($envFile, 'DB_USERNAME', $request->input('database_user'));
        $envFile = set_env_value($envFile, 'DB_PASSWORD', $request->input('database_password', ''));

        return $envFile;
    }

    /**
     * Configurar una base de datos SQLite en el archivo .env
     *
     * @param string $envFile
     * @return string
     */
    private function configureSqliteDatabase($envFile)
    {
        $envFile = set_env_value($envFile, 'DB_CONNECTION', 'sqlite');

        // Comentar otras variables de BD que no se usan con SQLite
        $envFile = comment_env_value($envFile, 'DB_HOST');
        $envFile = comment_env_value($envFile, 'DB_PORT');
        $envFile = comment_env_value($envFile, 'DB_DATABASE');
        $envFile = comment_env_value($envFile, 'DB_USERNAME');
        $envFile = comment_env_value($envFile, 'DB_PASSWORD');

        // Crear el archivo SQLite si no existe
        $sqlitePath = database_path('database.sqlite');
        if (!File::exists($sqlitePath)) {
            File::put($sqlitePath, '');
            chmod($sqlitePath, 0664);
        }

        return $envFile;
    }

    /**
     * Limpiar la caché de configuración
     *
     * @return void
     */
    private function clearConfigCache()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }
}
