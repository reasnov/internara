<?php

use Illuminate\Support\Facades\Log;

/**
 * This file provides a global fallback for the setting() helper function.
 *
 * It is loaded by the AppServiceProvider and defines a safe, non-functional
 * version of setting() if the real function from the Settings module is not
 * available. This prevents fatal errors across the application if the
 * Settings module is disabled.
 */
if (class_exists('\\Modules\\Settings\\Providers\\SettingsServiceProvider') && !function_exists('setting')) {
    /**
     * Fallback for the setting() helper function.
     *
     * This ensures that calls to setting() do not cause a fatal error if the
     * Settings module is disabled. It logs a warning and returns the default
     * value.
     */
    function setting(string|array $key = '', mixed $default = null): mixed
    {
        static $hasLogged = false;
        if (! $hasLogged) {
            Log::warning('The setting() helper was called, but the Settings module is not available. A fallback was used.');
            $hasLogged = true;
        }

        if (is_array($key)) {
            // Cannot set settings if the module is down
            return false;
        }

        if (empty($key)) {
            // Cannot return the repository instance
            return null;
        }

        // Return the default value when getting a setting
        return $default;
    }
}
