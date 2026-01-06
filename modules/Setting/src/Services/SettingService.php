<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Setting\Contracts\Services\SettingService as SettingServiceContract;
use Modules\Setting\Models\Setting;

class SettingService implements SettingServiceContract
{
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function set(string|array $key, mixed $value = null, array $extraAttributes = []): bool
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
     * {@inheritDoc}
     */
    public function getByGroup(string $name): \Illuminate\Support\Collection
    {
        $cacheKey = 'settings.group.'.$name;

        return Cache::remember($cacheKey, now()->addDay(), function () use ($name) {
            return Setting::group($name)->get();
        });
    }
}
