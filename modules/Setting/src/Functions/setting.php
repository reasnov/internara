<?php

use Modules\Setting\Facades\Setting;

if (! function_exists('setting')) {

    /**
     * Get application settings.
     */
    function setting(string|array|null $key = null, mixed $default = null, bool $skipCache = false): mixed
    {
        if ($key === null) {
            return app(Setting::class);
        }

        return app(Setting::class)->get($key, $default, $skipCache);
    }
}
