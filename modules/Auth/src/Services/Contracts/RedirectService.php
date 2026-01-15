<?php

declare(strict_types=1);

namespace Modules\Auth\Services\Contracts;

use Modules\User\Models\User;

/**
 * Interface RedirectService
 *
 * Defines the contract for determining where a user should be redirected
 * after successful authentication or registration.
 */
interface RedirectService
{
    /**
     * Get the target URL for a user based on their roles.
     *
     * @param  User  $user  The authenticated user.
     * @return string The absolute URL to redirect to.
     */
    public function getTargetUrl(User $user): string;
}