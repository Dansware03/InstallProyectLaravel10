<?php
namespace Dansware\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class NotInstalledMiddleware
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
        // Si la aplicación ya está instalada, redirigir a la página de "ya instalado"
        if (File::exists(storage_path('.installed'))) {
            return redirect()->route('installation.installed');
        }

        return $next($request);
    }
}