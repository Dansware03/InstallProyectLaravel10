<?php

if (!function_exists('installer_asset')) {
    /**
     * Generate an asset path for the installer package.
     *
     * @param string $path
     * @return string
     */
    function installer_asset($path)
    {
        return asset('vendor/installer/' . ltrim($path, '/'));
    }
}

if (!function_exists('installer_config')) {
    /**
     * Get installer configuration value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function installer_config($key, $default = null)
    {
        return config('installer.' . $key, $default);
    }
}

if (!function_exists('is_app_installed')) {
    /**
     * Check if the application is installed.
     *
     * @return bool
     */
    function is_app_installed()
    {
        return app('installer')->isInstalled();
    }
}