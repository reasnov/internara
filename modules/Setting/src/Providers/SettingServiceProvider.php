<?php

namespace Modules\Setting\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Setting\Contracts\Services\SettingService as SettingServiceContract;
use Modules\Setting\Services\SettingService;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;

class SettingServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider;

    protected string $name = 'Setting';

    protected string $nameLower = 'setting';

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

        // Include the global helper function
        require_once module_path($this->name, 'src/Functions/setting.php');
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
            SettingServiceContract::class => SettingService::class,
        ];
    }
}
