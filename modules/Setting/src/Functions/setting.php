<?php

use Modules\Setting\Facades\Setting;

if (! function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array of key/value pairs is given, the settings will be set.
     *
     * If a key is given, the setting value will be returned.
     *
     * If no key is given, the Setting facade instance will be returned.
     *
     * @return mixed|\Modules\Setting\Facades\Setting
     */
    function setting(string|array $key = '', mixed $default = null): mixed
    {
        if (empty($key)) {
            return app(Setting::class); // Return the facade instance
        }

        if (is_array($key)) {
            // Assume associative array for setting multiple values
            foreach ($key as $k => $v) {
                Setting::set($k, $v);
            }

            return true;
        }

        return Setting::get($key, $default);
    }
}
