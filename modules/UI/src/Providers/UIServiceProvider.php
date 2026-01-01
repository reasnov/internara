<?php

namespace Modules\UI\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;
use Modules\UI\Contracts\Core\SlotManager as SlotManagerContract;
use Modules\UI\Contracts\Core\SlotRegistry as SlotRegistryContract;
use Modules\UI\Core\SlotManager;
use Modules\UI\Core\SlotRegistry;
use Modules\UI\Facades\SlotRegistry as SlotRegistryFacade;
use Nwidart\Modules\Traits\PathNamespace;

class UIServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider;
    use PathNamespace;

    protected string $name = 'UI';

    protected string $nameLower = 'ui';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Register the custom Blade directive
        Blade::directive('slotRender', function ($expression) {
            return "<?php echo \Modules\UI\Facades\SlotManager::render({$expression}); ?>";
        });

        $this->registerDefaultNavbarSlots();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerBindings();
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Get the service bindings for the module.
     *
     * @return array<string, string|\Closure>
     */
    protected function bindings(): array
    {
        return [
            SlotManagerContract::class => SlotManager::class,
            SlotRegistryContract::class => SlotRegistry::class,
        ];
    }

    /**
     * Register default components for the main navbar slots.
     */
    private function registerDefaultNavbarSlots(): void
    {
        SlotRegistryFacade::register('navbar.brand', fn () => view('ui::components.brand'));

        SlotRegistryFacade::register('navbar.actions', function () {
            return view('ui::livewire.theme-toggle') . view('ui::components.user-dropdown');
        });
    }
}
