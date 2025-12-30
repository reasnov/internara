<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;
use Nwidart\Modules\Traits\PathNamespace;

class UserServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider;
    use PathNamespace;

    protected string $name = 'User';

    protected string $nameLower = 'user';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        // Keep module specific logic:
        $this->registerPolicies();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerBindings();
        // Keep module specific logic:
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
    }

    /**
     * Register the module policies.
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::policy(
            \Modules\User\Models\User::class,
            \Modules\User\Policies\UserPolicy::class
        );
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
}
