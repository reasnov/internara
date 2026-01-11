<?php

namespace Modules\Internship\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;
use Nwidart\Modules\Traits\PathNamespace;

class InternshipServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider;
    use PathNamespace;

    protected string $name = 'Internship';

    protected string $nameLower = 'internship';

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
            \Modules\Internship\Contracts\Services\InternshipService::class => \Modules\Internship\Services\InternshipService::class
        ];
    }
}
