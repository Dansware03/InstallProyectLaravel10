<?php
namespace Dansware\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
        // Si no está instalado y NO está en la ruta de instalación, redirigir al instalador
        if (!File::exists(storage_path('.installed')) && !$request->is(config('installer.route', 'install').'*')) {
            return redirect()->route('installation.welcome');
        }

        // Si está instalado y está en la ruta de instalación, redirigir a la página principal
        if (File::exists(storage_path('.installed')) && $request->is(config('installer.route', 'install').'*') && !$request->is(config('installer.route', 'install').'/installed')) {
            return redirect('/');
        }

        return $next($request);
    }
}