<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class FinishController extends BaseController
{
    /**
     * Muestra el formulario final de la instalación
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if ($redirect = $this->checkIfInstalled()) {
            return $redirect;
        }

        $steps = $this->getInstallationSteps();
        $runMigrations = config('installer.post_installation.run_migrations', true);
        $runSeeders = config('installer.post_installation.run_seeders', false);
        $createAdmin = config('installer.post_installation.create_admin', false);

        return view('installer::installation.finish', compact('steps', 'runMigrations', 'runSeeders', 'createAdmin'));
    }

    /**
     * Finaliza la instalación
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
            $request->validate([
                'run_migrations' => 'nullable|boolean',
                'run_seeders' => 'nullable|boolean',
                'create_admin' => 'nullable|boolean',
                'admin_email' => 'required_if:create_admin,1|email',
                'admin_password' => 'required_if:create_admin,1|min:8',
            ]);

            // Ejecutar migraciones si se solicitó
            if ($request->boolean('run_migrations', config('installer.post_installation.run_migrations', true))) {
                $this->runMigrations($request->boolean('run_seeders', config('installer.post_installation.run_seeders', false)));
            }

            // Crear usuario administrador si se solicitó
            if ($request->boolean('create_admin', config('installer.post_installation.create_admin', false))) {
                $this->createAdminUser(
                    $request->input('admin_email'),
                    $request->input('admin_password')
                );
            }

            // Optimizar la aplicación
            $this->optimizeApplication();

            // Crear archivo .installed para indicar que la instalación está completa
            $this->markAsInstalled();

            // Limpiar caché
            $this->clearApplicationCache();

            // Redirigir a la URL configurada o a la raíz
            $redirectUrl = config('installer.post_installation.redirect_url', '/');
            return redirect($redirectUrl)->with('success', 'Instalación completada con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al finalizar la instalación: ' . $e->getMessage());
        }
    }

    /**
     * Muestra página de instalación completada
     *
     * @return \Illuminate\View\View
     */
    public function installed()
    {
        return view('installer::installation.installed');
    }

    /**
     * Ejecutar migraciones y seeders
     *
     * @param bool $withSeeders
     * @return void
     */
    private function runMigrations($withSeeders = false)
    {
        // Ejecutar migraciones
        Artisan::call('migrate', ['--force' => true]);

        // Ejecutar seeders si se solicitó
        if ($withSeeders) {
            Artisan::call('db:seed', ['--force' => true]);
        }
    }

    /**
     * Crear usuario administrador
     *
     * @param string $email
     * @param string $password
     * @return void
     */
    private function createAdminUser($email, $password)
    {
        // Este método puede variar según la estructura de tu aplicación
        // Por defecto intentaremos usar el modelo User
        if (class_exists('App\Models\User')) {
            $userModel = 'App\Models\User';
        } elseif (class_exists('App\User')) {
            $userModel = 'App\User';
        } else {
            return; // No se puede determinar el modelo de usuario
        }

        $user = new $userModel();
        $user->name = 'Administrator';
        $user->email = $email;
        $user->password = bcrypt($password);

        // Comprobar si el modelo tiene roles o permisos
        if (method_exists($user, 'assignRole')) {
            $user->save();
            $user->assignRole('admin');
        } else {
            // Asignar is_admin o admin si existe
            if (in_array('is_admin', $user->getFillable()) || $user->getConnection()->getSchemaBuilder()->hasColumn($user->getTable(), 'is_admin')) {
                $user->is_admin = true;
            } elseif (in_array('admin', $user->getFillable()) || $user->getConnection()->getSchemaBuilder()->hasColumn($user->getTable(), 'admin')) {
                $user->admin = true;
            }

            $user->save();
        }
    }

    /**
     * Optimizar la aplicación para producción
     *
     * @return void
     */
    private function optimizeApplication()
    {
        // Solo optimizamos en producción
        if (app()->environment('production')) {
            try {
                Artisan::call('optimize');
                Artisan::call('view:cache');
                Artisan::call('route:cache');
                Artisan::call('config:cache');
            } catch (\Exception $e) {
                // Algunos comandos podrían no estar disponibles en ciertas versiones
                // Continuamos sin lanzar excepciones
            }
        }
    }

    /**
     * Marcar la aplicación como instalada
     *
     * @return void
     * @throws \Exception
     */
    private function markAsInstalled()
    {
        try {
            File::put(storage_path('.installed'), date('Y-m-d H:i:s'));
            chmod(storage_path('.installed'), 0644); // Asegurar permisos adecuados
        } catch (\Exception $e) {
            throw new \Exception('No se pudo crear el archivo de instalación: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar todas las cachés de la aplicación
     *
     * @return void
     */
    private function clearApplicationCache()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }
}
