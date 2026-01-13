<?php

namespace Modules\Shared\Providers\Concerns;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\UI\Facades\SlotRegistry;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait ManagesModuleProvider
{
    /**
     * Register the module's service bindings.
     */
    protected function registerBindings(): void
    {
        // Logic from ManagesBindings trait
        foreach ($this->bindings() as $abstract => $concrete) {
            if (! $this instanceof ServiceProvider) {
                throw new \LogicException('The ManagesModuleProvider trait must be used in a class that extends Illuminate\Support\ServiceProvider.');
            }
            if (is_string($concrete) && (new \ReflectionClass($concrete))->isInstantiable()) {
                // Assume singleton if concrete is a class name and instantiable
                $this->app->singleton($abstract, $concrete);
            } else {
                // Or bind if it's a closure or non-instantiable class name (e.g., interface)
                $this->app->bind($abstract, $concrete);
            }
        }
    }

    /**
     * Get the service bindings for the module.
     *
     * @return array<string, string|\Closure>
     */
    protected function bindings(): array
    {
        return [];
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    protected function registerTranslations(): void
    {
        // Use $this->nameLower from the concrete provider
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        // Use $this->name from the concrete provider
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    // Use $this->nameLower from the concrete provider
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        // Use $this->nameLower from the concrete provider
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        // Use $this->name from the concrete provider
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\'.$this->name.'\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            // Use $this->nameLower from the concrete provider
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }

    /**
     * Register components for the main navbar slots.
     */
    protected function registerViewSlots(): void
    {
        SlotRegistry::configure($this->viewSlots());
    }

    protected function viewSlots(): array
    {
        return [];
    }
}
