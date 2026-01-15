<?php

declare(strict_types=1);

namespace Modules\Auth\Services;

use Modules\Auth\Services\Contracts\RedirectService as Contract;
use Modules\User\Models\User;

/**
 * Class RedirectService
 *
 * Handles logic for determining the destination URL after login/registration.
 */
class RedirectService implements Contract
{
    /**
     * Get the target URL for a user based on their roles.
     */
    public function getTargetUrl(User $user): string
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return route('dashboard.admin');
        }

        if ($user->hasRole('teacher')) {
            return route('dashboard.teacher');
        }

        if ($user->hasRole('student')) {
            return route('dashboard'); // Assuming 'dashboard' is the student dashboard
        }

        return route('dashboard');
    }
}