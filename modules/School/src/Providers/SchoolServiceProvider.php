<?php

namespace Modules\School\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;
use Nwidart\Modules\Traits\PathNamespace;

class SchoolServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider;
    use PathNamespace;

    protected string $name = 'School';

    protected string $nameLower = 'school';

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
        $this->registerViewSlots();
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
            \Modules\School\Contracts\Services\SchoolService::class => \Modules\School\Services\SchoolService::class,
        ];
    }

    protected function viewSlots(): array
    {
        return [
            'school-manager' => 'livewire:school::school-manager',
        ];
    }
}
