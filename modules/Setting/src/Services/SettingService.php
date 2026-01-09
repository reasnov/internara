<?php

namespace Modules\Setting\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Setting\Contracts\Services\SettingService as SettingServiceContract;
use Modules\Setting\Models\Setting;

class SettingService implements SettingServiceContract
{
    /**
     * The Setting model instance.
     *
     * @var \Modules\Setting\Models\Setting
     */
    protected Setting $model;

    /**
     * Create a new SettingService instance.
     *
     * @param  \Modules\Setting\Models\Setting  $model The Setting model instance.
     */
    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    /**
     * Get all settings.
     *
     * @param  array  $columns  The columns to select.
     * @param  bool  $skipCache  Whether to bypass the cache and fetch directly from the database.
     * @return \Illuminate\Support\Collection A collection of all Setting models.
     */
    public function all(array $columns = ['*'], bool $skipCache = false): Collection
    {
        return $this->remember('settings.all', fn () => $this->model->all($columns), $skipCache);
    }

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

        return $this->remember('settings.'.$key, function () use ($key, $default) {
            $setting = $this->model->find($key);

            if (! $setting) {
                return $default;
            }

            return $setting->value;
        }, $skipCached);
    }

    /**
     * {@inheritDoc}
     */
    public function set(string|array $key, mixed $value = null, array $extraAttributes = []): bool
    {
        if (is_array($key)) {
            $success = true;
            foreach ($key as $k => $v) {
                $value = $v['value'] ?? $v;
                $extraAttributes = is_array($v) ? array_diff_key($v, ['value' => null]) : [];
                if (! $this->set($k, $value, $extraAttributes)) {
                    $success = false;
                }
            }

            return $success;
        }

        $setting = $this->model->updateOrCreate(
            ['key' => $key],
            array_merge(
                ['value' => $value],
                $extraAttributes
            )
        );

        $this->clearCache($key, $setting->group);

        return (bool) $setting;
    }

    /**
     * {@inheritDoc}
     */
    public function getByGroup(string $name, bool $skipCache = false): Collection
    {
        return $this->remember('settings.group.'.$name, fn () => $this->model->group($name)->get(), $skipCache);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        $setting = $this->model->find($key);

        if (! $setting) {
            return false;
        }

        if ($deleted = $setting->delete()) {
            $this->clearCache($key, $setting->group);
        }

        return $deleted;
    }

    /**
     * {@inheritDoc}
     */
    public function query(array $columns = ['*']): Builder
    {
        return $this->model->query()->select($columns);
    }

    /**
     * Retrieve an item from the cache or store the result of a callback forever.
     *
     * @param string $cacheKey The key for the cache item.
     * @param callable $callback The callback to execute if the item is not in the cache.
     * @param bool $skipCache Whether to skip the cache and execute the callback directly.
     * @return mixed
     */
    protected function remember(string $cacheKey, callable $callback, bool $skipCache = false): mixed
    {
        if ($skipCache) {
            Cache::forget($cacheKey);

            return $callback();
        }

        return Cache::remember($cacheKey, now()->addDay(), $callback);
    }

    /**
     * Clear the cache for a specific setting and its related groups.
     *
     * @param string $key The key of the setting whose cache to clear.
     * @param string|null $group The group name the setting belongs to, if any.
     * @return void
     */
    protected function clearCache(string $key, ?string $group = null): void
    {
        // Clear cache for this specific setting
        Cache::forget('settings.'.$key);

        // Clear cache for the group this setting belongs to, if any
        if ($group) {
            Cache::forget('settings.group.'.$group);
        }

        // Clear cache for all settings, as this setting might affect the 'all' collection
        Cache::forget('settings.all');
    }
}
