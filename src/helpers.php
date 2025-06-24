<?php

if (!function_exists('installer_asset')) {
    /**
     * Generate an asset path for the installer.
     */
    function installer_asset(string $path): string
    {
        return asset('vendor/installer/' . ltrim($path, '/'));
    }
}

if (!function_exists('installer_config')) {
    /**
     * Get installer configuration value.
     */
    function installer_config(string $key, $default = null)
    {
        return config("installer.{$key}", $default);
    }
}