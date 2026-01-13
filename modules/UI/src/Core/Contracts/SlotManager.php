<?php

namespace Modules\UI\Core\Contracts;

interface SlotManager
{
    /**
     * Render all registered components for a given slot.
     *
     * @param  string  $slot  The name of the slot to render.
     * @return string The rendered components.
     */
    public function render(string $slot): string;
}
