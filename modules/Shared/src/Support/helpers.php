<?php

declare(strict_types=1);

if (! function_exists('is_active_module')) {
    /**
     * Check if a module is active.
     */
    function is_active_module(string $name): bool
    {
        return Module::isEnabled($name);
    }
}

if (! function_exists('shared_static_url')) {
    /**
     * Get the URL for a shared static asset.
     */
    function shared_static_url(string $path): string
    {
        return asset('modules/shared/'.ltrim($path, '/'));
    }
}
