<?php

namespace Modules\FormBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use Modules\Shared\Concerns\Providers\ManagesModuleProvider;

class FormBuilderServiceProvider extends ServiceProvider
{
    use PathNamespace;
    use ManagesModuleProvider;

    protected string $name = 'FormBuilder';
    protected string $nameLower = 'formbuilder';

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
            // 'SomeContract::class' => 'SomeConcrete::class'
        ];
    }
}
