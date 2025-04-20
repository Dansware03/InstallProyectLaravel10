<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Http\Request;

class RequirementsController extends BaseController
{
    /**
     * Verifica los requisitos del sistema
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if ($redirect = $this->checkIfInstalled()) {
            return $redirect;
        }

        $requirements = $this->checkRequirements();
        $allRequirementsMet = !in_array(false, array_column($requirements, 'result'));
        $steps = $this->getInstallationSteps();

        return view('installer::installation.requirements', compact('requirements', 'allRequirementsMet', 'steps'));
    }

    /**
     * Verifica los requisitos del sistema
     *
     * @return array
     */
    private function checkRequirements()
    {
        $config = config('installer.requirements');
        $requirements = [];

        // Verificar versión de PHP
        $phpVersion = $config['php']['version'] ?? '8.2.0';
        $requirements['php_version'] = [
            'name' => 'Versión de PHP >= ' . $phpVersion,
            'result' => version_compare(PHP_VERSION, $phpVersion, '>='),
            'current' => PHP_VERSION
        ];

        // Verificar extensiones de PHP
        $phpExtensions = $config['php']['extensions'] ?? [];
        foreach ($phpExtensions as $extension) {
            $requirements[$extension] = [
                'name' => ucfirst($extension) . ' PHP Extension',
                'result' => extension_loaded($extension),
                'current' => extension_loaded($extension) ? 'Instalado' : 'No instalado'
            ];
        }

        // Verificar permisos de directorios y archivos
        $permissions = $config['permissions'] ?? [];
        foreach ($permissions as $path => $permission) {
            $fullPath = base_path($path);
            $isWritable = is_writable($fullPath);
            $requirements['permission_' . str_replace(['/', '.'], '_', $path)] = [
                'name' => 'Permisos en ' . $path,
                'result' => $isWritable,
                'current' => $isWritable ? 'Escribible' : 'No escribible'
            ];
        }

        // Información del servidor
        $requirements['server_software'] = [
            'name' => 'Software de servidor',
            'result' => true,
            'current' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'
        ];

        return $requirements;
    }
}
