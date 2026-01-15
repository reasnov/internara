<?php

declare(strict_types=1);

namespace Modules\Setup\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Setup\Services\Contracts\SetupService;
use Modules\User\Services\Contracts\SuperAdminService;

class ProtectSetupRoute
{
    public function __construct(protected SetupService $setupService, protected SuperAdminService $superAdminService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isNotValidAccess()) {
            return abort(404);
        }

        return $next($request);
    }

    protected function isNotValidAccess(): bool
    {
        return $this->setupService->isAppInstalled()
            && $this->superAdminService->remember(
                cacheKey: 'user.super_admin',
                ttl: now()->addDay(),
                callback: fn (SuperAdminService $service) => $service->exists()
            );
    }
}
