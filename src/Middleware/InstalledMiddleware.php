<?php
namespace Dansware\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InstalledMiddleware
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
        // Si la aplicación NO está instalada, redirigir al instalador
        if (!File::exists(storage_path('.installed'))) {
            return redirect()->route('installation.welcome');
        }

        return $next($request);
    }
}