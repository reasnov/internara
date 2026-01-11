<?php

namespace Modules\Setting\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Setting\Contracts\Services\SettingService;

/**
 * @method static mixed get(string|array $key, mixed $default = null, bool $skipCached = false)
 * @method static bool set(string|array $key, mixed $value, array $extraAttributes = [])
 * @method static \Illuminate\Support\Collection getByGroup(string $name)
 *
 * @see \Modules\Setting\Services\SettingService
 */
class Setting extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SettingService::class;
    }
}
