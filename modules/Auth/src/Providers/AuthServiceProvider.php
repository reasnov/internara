<?php

namespace Modules\Auth\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as BaseAuthServiceProvider;
use Modules\Shared\Providers\Concerns\ManagesModuleProvider;
use Nwidart\Modules\Traits\PathNamespace;

class AuthServiceProvider extends BaseAuthServiceProvider
{
    use ManagesModuleProvider;
    use PathNamespace;

    protected string $name = 'Auth';

    protected string $nameLower = 'auth';

    /**
     * Boot the authentication / authorization services.
     */
    public function boot(): void
    {
        // Add module boot logic
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerViewSlots();
    }

    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        $this->registerBindings();
        // Register other service providers from this (Auth) module
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
            \Modules\Auth\Services\Contracts\AuthService::class => \Modules\Auth\Services\AuthService::class,
        ];
    }

    protected function viewSlots(): array
    {
        return [
            // This needs to be updated. Livewire components are moving to Auth module
            'register.owner' => 'livewire:auth::register-owner', // Changed 'user' to 'auth', removed 'auth' subdir
        ];
    }
}
