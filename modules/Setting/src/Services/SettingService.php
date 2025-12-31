<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Setting\Contracts\Services\SettingService as SettingServiceContract;
use Modules\Setting\Models\Setting;

class SettingService implements SettingServiceContract
{
    /**
     * Get a setting value by key or an array of keys.
     *
     * @param  string|array  $key  The key or keys of the setting(s) to retrieve.
     * @param  mixed  $default  A default value to return if the setting is not found.
     * @param  bool  $skipCached  Whether to bypass the cache and fetch directly from the database.
     * @return mixed The setting value(s) or the default value.
     */
    public function get(string|array $key, mixed $default = null, bool $skipCached = false): mixed
    {
        if (is_array($key)) {
            $results = [];
            foreach ($key as $k) {
                $results[$k] = $this->get($k, $default, $skipCached);
            }

            return $results;
        }

        $cacheKey = 'settings.'.$key;

        if ($skipCached) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addDay(), function () use ($key, $default) {
            $setting = Setting::find($key);

            if (! $setting) {
                return $default;
            }

            return $setting->value;
        });
    }

    /**
     * Set a setting value.
     *
     * @param  string|array  $key  The key of the setting to set. If an array, it's treated as key-value pairs.
     * @param  mixed  $value  The value to set for the setting.
     * @param  array  $extraAttributes  Additional attributes (type, description, group) for the setting.
     * @return bool True if the setting was successfully set, false otherwise.
     */
    public function set(string|array $key, mixed $value, array $extraAttributes = []): bool
    {
        if (is_array($key)) {
            $success = true;
            foreach ($key as $k => $v) {
                if (! $this->set($k, $v, $extraAttributes[$k] ?? [])) {
                    $success = false;
                }
            }

            return $success;
        }

        // The cast handles value stringification and type attribute setting.
        // We only pass the raw value and any other extra attributes.
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            array_merge(
                ['value' => $value], // Pass the raw value, cast will process it
                $extraAttributes
            )
        );

        // Clear cache for this specific setting
        Cache::forget('settings.'.$key);
        // Invalidate cache for any group this setting might belong to
        Cache::forget('settings.group.'.($setting->group ?? ''));

        return (bool) $setting;
    }

    /**
     * Get all settings belonging to a specific group.
     *
     * @param  string  $name  The name of the group to retrieve settings from.
     * @return \Illuminate\Support\Collection|\Modules\Setting\Models\Setting[] A collection of Setting models.
     */
    public function getByGroup(string $name): \Illuminate\Support\Collection
    {
        $cacheKey = 'settings.group.'.$name;

        return Cache::remember($cacheKey, now()->addDay(), function () use ($name) {
            return Setting::group($name)->get();
        });
    }
}
