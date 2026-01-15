<?php

declare(strict_types=1);

namespace Modules\Setup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Modules\Setup\Services\Contracts\SetupService;
use Symfony\Component\HttpFoundation\Response;

class RequireSetupAccess
{
    public function __construct(protected SetupService $setupService) {}

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the app is installed and is setup route, redirect to login
        if ($this->setupService->isAppInstalled() && $this->isSetupRoute($request)) {
            return redirect()->route('login');
        }

        // If the app is not installed, redirect to the setup route.
        if (! $this->setupService->isAppInstalled() && ! $this->isSetupRoute($request)) {
            // Bypass specific requests
            if ($this->bypassSpecificRequests($request)) {
                return $next($request);
            }

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
     */
    private function isSetupRoute(Request $request): bool
    {
        return $request->routeIs('setup.*');
    }

    private function isLivewireRequest(Request $request): bool
    {
        // for UI Interaction and file upload
        return Livewire::isLivewireRequest() || $request->is('livewire/*');
    }
}
