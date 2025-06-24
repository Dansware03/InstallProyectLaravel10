<?php

namespace Dansware03\LaravelInstaller\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Dansware03\LaravelInstaller\InstallerManager;

class InstallationMiddleware
{
    /**
     * The installer manager instance.
     *
     * @var InstallerManager
     */
    protected $installer;

    /**
     * Create a new middleware instance.
     */
    public function __construct(InstallerManager $installer)
    {
        $this->installer = $installer;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si la aplicación no está instalada, redirigir al instalador
        if (!$this->installer->isInstalled() && !$request->is('install*')) {
            return redirect()->route('installer.welcome');
        }

        // Si la aplicación está instalada y se intenta acceder al instalador
        if ($this->installer->isInstalled() && $request->is('install*')) {
            return redirect('/');
        }

        return $next($request);
    }
}