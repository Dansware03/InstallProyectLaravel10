<?php

namespace Dansware\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si no está instalado y no estamos en la ruta de instalación
        if (!is_app_installed() && !$this->isInstallerRoute($request)) {
            return redirect()->route('installation.welcome');
        }

        return $next($request);
    }

    /**
     * Verificar si la ruta actual es una ruta del instalador
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isInstallerRoute(Request $request)
    {
        $installerPrefix = config('installer.route', 'install');
        return $request->is($installerPrefix) || $request->is($installerPrefix . '/*');
    }
}
