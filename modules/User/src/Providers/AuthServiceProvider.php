<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;
use Modules\User\Models\User;
use Modules\User\Policies\UserPolicy; // New Use Statement

class AuthServiceProvider extends ServiceProvider
{
    use ManagesModuleProvider; // Use the trait

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Boot the authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant all permissions to the 'owner' role.
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('owner') ? true : null;
        });
    }

    /**
     * Get the service bindings for the module.
     *
     * @return array<string, string|\Closure>
     */
    protected function bindings(): array
    {
        return []; // No explicit bindings for this AuthServiceProvider, but it adheres to the pattern
    }
}
