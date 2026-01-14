<?php

namespace Modules\Permission\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Permission\Contracts\PermissionManager as PermissionManagerContract;
use Modules\Permission\Services\PermissionManager;
use Modules\Shared\Providers\Concerns\ManagesModuleProvider;
use Modules\User\Models\User;
use Nwidart\Modules\Traits\PathNamespace;

class PermissionServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider;
    use PathNamespace;

    protected string $name = 'Permission';

    protected string $nameLower = 'permission';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->overrideSpatieConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerPolicies();
    }

    /**
     * Override Spatie Permission configuration at runtime.
     * This ensures the module is isolated and portable.
     */
    protected function overrideSpatieConfig(): void
    {
        config([
            'permission.models.role' => \Modules\Permission\Models\Role::class,
            'permission.models.permission' => \Modules\Permission\Models\Permission::class,
        ]);
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
     * Register the module policies.
     */
    protected function registerPolicies(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }

    /**
     * Get the service bindings for the module.
     *
     * @return array<string, string|\Closure>
     */
    protected function bindings(): array
    {
        return [
            PermissionManagerContract::class => PermissionManager::class,
        ];
    }
}
