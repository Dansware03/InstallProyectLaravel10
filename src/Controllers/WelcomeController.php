<?php

namespace Dansware\LaravelInstaller\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends BaseController
{
    /**
     * Muestra la pantalla de bienvenida
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if ($redirect = $this->checkIfInstalled()) {
            return $redirect;
        }

        $steps = $this->getInstallationSteps();

        return view('installer::installation.welcome', compact('steps'));
    }
}
