<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    /**
     * Verificar si la aplicación ya está instalada
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function checkIfInstalled()
    {
        if (is_app_installed()) {
            return redirect()->route('installation.installed');
        }

        return null;
    }

    /**
     * Obtiene los pasos de instalación desde la configuración
     *
     * @return array
     */
    protected function getInstallationSteps()
    {
        return config('installer.steps', []);
    }
}
