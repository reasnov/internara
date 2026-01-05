<?php

/**
 * This file provides a global fallback for the setting() helper function.
 *
 * It is loaded by the AppServiceProvider and defines a safe, non-functional
 * version of setting() if the real function from the Setting module is not
 * available. This prevents fatal errors across the application if the
 * Setting module is disabled.
 */
if (! function_exists('setting')) {
    /**
     * Fallback for the setting() helper function.
     *
     * This ensures that calls to setting() do not cause a fatal error if the
     * Setting module is disabled. It logs a warning and returns the default
     * value.
     */
    function setting(string|array $key = '', mixed $default = null): mixed
    {
        static $hasLogged = false;
        if (! $hasLogged) {
            \Illuminate\Support\Facades\Log::warning(
                'The setting() helper was called, but the Setting module is not available. A fallback was used.'
            );
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
