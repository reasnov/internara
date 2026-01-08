<?php

namespace Modules\Setting\Contracts\Services;

interface SettingService
{
    /**
     * Get a setting value by key or an array of keys.
     *
     * @param  string|array  $key  The key or keys of the setting(s) to retrieve.
     * @param  mixed  $default  A default value to return if the setting is not found.
     * @param  bool  $skipCached  Whether to bypass the cache and fetch directly from the database.
     * @return mixed The setting value(s) or the default value.
     */
    public function get(string|array $key, mixed $default = null, bool $skipCached = false): mixed;

    /**
     * Set a setting value.
     *
     * @param  string|array  $key  The key of the setting to set. If an array, it's treated as key-value pairs.
     * @param  mixed  $value  The value to set for the setting.
     * @param  array  $extraAttributes  Additional attributes (type, description, group) for the setting.
     * @return bool True if the setting was successfully set, false otherwise.
     */
    public function set(string|array $key, mixed $value = null, array $extraAttributes = []): bool;

    /**
     * Get all settings belonging to a specific group.
     *
     * @param  string  $name  The name of the group to retrieve settings from.
     * @return \Illuminate\Support\Collection|\Modules\Setting\Models\Setting[] A collection of Setting models.
     */
    public function getByGroup(string $name): \Illuminate\Support\Collection;

    public function delete(string $key): bool;
}
