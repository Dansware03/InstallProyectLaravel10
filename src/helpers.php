<?php

if (!function_exists('installer_assets')) {
    /**
     * Genera la URL para un asset en el directorio de assets del instalador.
     *
     * @param string $path
     * @return string
     */
    function installer_assets($path)
    {
        return asset('vendor/installer/' . $path);
    }
}

if (!function_exists('is_app_installed')) {
    /**
     * Verifica si la aplicación ya está instalada.
     *
     * @return bool
     */
    function is_app_installed()
    {
        return file_exists(storage_path('.installed'));
    }
}

if (!function_exists('set_env_value')) {
    /**
     * Actualiza o agrega una variable en el contenido de .env
     *
     * @param string $envFile
     * @param string $key
     * @param string $value
     * @return string
     */
    function set_env_value($envFile, $key, $value)
    {
        // Buscamos la variable, ya sea comentada o no
        $pattern = "/^(#\s*)?{$key}=.*/m";
        $replacement = "{$key}={$value}";
        if (preg_match($pattern, $envFile)) {
            return preg_replace($pattern, $replacement, $envFile);
        } else {
            // Si la variable no existe, la agregamos al final
            return $envFile . "\n" . $replacement;
        }
    }
}

if (!function_exists('comment_env_value')) {
    /**
     * Comenta o actualiza una variable en el contenido de .env
     *
     * @param string $envFile
     * @param string $key
     * @param string $value
     * @return string
     */
    function comment_env_value($envFile, $key, $value)
    {
        $pattern = "/^(#\s*)?{$key}=.*/m";
        $replacement = "#{$key}={$value}";
        if (preg_match($pattern, $envFile)) {
            return preg_replace($pattern, $replacement, $envFile);
        } else {
            // Si la variable no existe, la agregamos al final
            return $envFile . "\n" . $replacement;
        }
    }
}