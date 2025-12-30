<?php

namespace Modules\UI\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\UI\Contracts\Core\SlotManager as SlotManagerContract;

/**
 * @method static string render(string $slot)
 *
 * @see \Modules\UI\Core\SlotManager
 */
class SlotManager extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SlotManagerContract::class;
    }
}
