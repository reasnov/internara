<?php

declare(strict_types=1);

namespace Modules\User\Policies;

use Modules\User\Models\User;

/**
 * Class UserPolicy
 *
 * Controls access to User model operations.
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('user.view') || $user->can('user.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('user.view') || $user->can('user.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('user.create') || $user->can('user.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('user.update') || $user->can('user.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can('user.delete') || $user->can('user.manage');
    }
}
