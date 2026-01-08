<?php

namespace Modules\Setup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Modules\Setup\Contracts\Services\SetupService;
use Symfony\Component\HttpFoundation\Response;

class RequireSetupAccess
{
    /**
     * @param  \Modules\Setup\Contracts\Services\SetupService  $setupService
     */
    public function __construct(protected SetupService $setupService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass specific requests
        if ($this->bypassSpecificRequests($request)) {
            return $next($request);
        }

        if ($this->setupService->isAppInstalled() && $this->isSetupRoute($request)) {
            return redirect()->route('login');
        }

        // If the app is not installed, redirect to the setup wizard.
        if (!$this->setupService->isAppInstalled() && !$this->isSetupRoute($request)) {
            return redirect()->route('setup.welcome');
        }

        return $next($request);
    }

    protected function bypassSpecificRequests(Request $request): bool
    {
        return app()->runningInConsole() || $this->isLivewireRequest($request);
    }

    /**
     * Check if the current request is for a setup route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isSetupRoute(Request $request): bool
    {
        return $request->routeIs('setup.*');
    }

    private function isLivewireRequest(Request $request): bool
    {
        return Livewire::isLivewireRequest() || $request->is('livewire/*');
    }
}
