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

        if ($skipCached) {
            $this->forgetKey($key);
        }

        return $this->rememberKey($key, function () use ($key, $default) {
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

        $this->forgetKey($key, $setting->group ?? null);

        return (bool) $setting;
    }

    /**
     * {@inheritDoc}
     */
    public function getByGroup(string $name): \Illuminate\Support\Collection
    {
        return $this->rememberKey($name, fn () => Setting::group($name)->get(), isGroup: true);
    }

    public function delete(string $key): bool
    {
        $setting = Setting::findOrFail($key);

        if ($deleted = $setting->delete()) {
            $this->forgetKey($key);
        }

        return $deleted;
    }

    private function rememberKey(string $key, mixed $callback = null, bool $isGroup = false): mixed
    {
        $cacheKey = $isGroup ? "settings.group.{$key}" : "settings.{$key}";

        return Cache::remember($cacheKey, now()->addDay(), function () use ($callback) {
            if (is_callable($callback)) {
                return $callback();
            }

            return $callback;
        });
    }

    private function forgetKey(string $key, ?string $group = null): void
    {
        // Clear cache for this specific setting
        Cache::forget('settings.'.$key);
        // Invalidate cache for any group this setting might belong to
        Cache::forget('settings.group.'.($group ?? ''));
    }
}
