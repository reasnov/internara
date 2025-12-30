<?php

namespace Modules\UI\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\UI\Contracts\Core\SlotRegistry as SlotRegistryContract;

/**
 * @method static void register(string $slot, string|\Closure|\Illuminate\Contracts\View\View $view, array $data = [])
 *
 * @see \Modules\UI\Core\SlotRegistry
 */
class SlotRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SlotRegistryContract::class;
    }
}
