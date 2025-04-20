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
     * Verifica si la aplicación ya está instalada
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
     * Establece un valor en el archivo .env
     *
     * @param string $envFile El contenido del archivo .env
     * @param string $key La clave a establecer o actualizar
     * @param string $value El valor a asignar
     * @return string
     */
    function set_env_value($envFile, $key, $value)
    {
        // Escapar caracteres especiales en el valor
        $value = is_string($value) ? '"' . addcslashes($value, '"\\') . '"' : $value;

        // Verificar si la clave ya existe para actualizarla
        if (preg_match("/^{$key}=.*/m", $envFile)) {
            return preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envFile);
        }

        // Si la clave está comentada, descomentarla y establecer el valor
        if (preg_match('/^# ' . preg_quote($key, '/') . '=.*/m', $envFile)) {
            return preg_replace('/^# ' . preg_quote($key, '/') . '=.*/m', "{$key}={$value}", $envFile);
        }

        // Si no existe, añadirla al final del archivo
        return $envFile . "\n{$key}={$value}\n";
    }
}

if (!function_exists('comment_env_value')) {
    /**
     * Comenta una variable en el archivo .env
     *
     * @param string $envFile El contenido del archivo .env
     * @param string $key La clave a comentar
     * @param string $value El valor por defecto si no existe
     * @return string
     */
    function comment_env_value($envFile, $key, $value = null)
    {
        // Si la clave ya existe, comentarla
        if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $envFile)) {
            return preg_replace('/^' . preg_quote($key, '/') . '=.*/m', "# {$key}={$value}", $envFile);
        }

        // Si ya está comentada, no hacer nada
        if (preg_match("/^# {$key}=.*/m", $envFile)) {
            return $envFile;
        }

        // Si no existe, añadirla comentada al final del archivo
        if ($value !== null) {
            return $envFile . "\n# {$key}=" . $value . "\n";
        }

        return $envFile;
    }
}
if (!function_exists('get_env_value')) {
    /**
     * Obtiene un valor del archivo .env
     *
     * @param string $key La clave a buscar
     * @param string $default Valor por defecto si no se encuentra
     * @return string|null
     */
    function get_env_value($key, $default = null)
    {
        $envFile = file_get_contents(base_path('.env'));
        preg_match("/^{$key}=[\"']?([^\"'\n]+)[\"']?/m", $envFile, $matches);

        return $matches[1] ?? $default;
    }
}
