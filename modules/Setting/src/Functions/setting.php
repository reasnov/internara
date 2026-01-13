<?php

use Modules\Setting\Facades\Setting;

if (! function_exists('setting')) {
    /**
     * Get application settings.
     */
    function setting(string|array|null $key = null, mixed $default = null, bool $skipCache = false): mixed
    {
        $settingFacade = app(Setting::class);

        if ($key === null) {
            return $settingFacade::getFacadeRoot();
        }

        return $settingFacade::getValue($key, $default, $skipCache);
    }
}
