<?php

namespace Modules\UI\Core;

use Closure;
use Illuminate\Contracts\View\View;
use Modules\UI\Contracts\Core\SlotRegistry as SlotRegistryContract;

/**
 * Handles the registration of UI components into named slots.
 * This class is intended to be used as a singleton to collect registrations
 * from various parts of the application.
 */
class SlotRegistry implements SlotRegistryContract
{
    /**
     * The array of registered slots.
     */
    protected array $slots = [];

    /**
     * Register a renderable component for a given slot.
     *
     * @param  string  $slot  The name of the slot.
     * @param  string|Closure|View  $view  The component to render.
     * @param  array  $data  Optional data to pass to the component.
     */
    public function register(string $slot, string|Closure|View $view, array $data = []): void
    {
        $this->slots[$slot][] = [
            'view' => $view,
            'data' => $data,
        ];
    }

    /**
     * Get all registered components for a given slot.
     *
     * @param  string  $slot  The name of the slot.
     * @return array The registered components.
     */
    public function getSlotsFor(string $slot): array
    {
        return $this->slots[$slot] ?? [];
    }
}
