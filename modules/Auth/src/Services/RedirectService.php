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
     * Get the target URL for a user based on their roles and status.
     */
    public function getTargetUrl(User $user): string
    {
        // If the user is not an admin and hasn't verified their email,
        // redirect them to the verification notice page.
        if (! $user->hasAnyRole(['super-admin', 'admin']) && ! $user->hasVerifiedEmail()) {
            return route('verification.notice');
        }

        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return route('dashboard.admin');
        }

        if ($user->hasRole('teacher')) {
            return route('dashboard.teacher');
        }

        if ($user->hasRole('student')) {
            return route('dashboard');
        }

        return route('dashboard');
    }
}