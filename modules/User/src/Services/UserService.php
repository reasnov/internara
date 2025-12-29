<?php

namespace Modules\User\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\UserService as UserServiceContract;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
    /**
     * List users with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($filters['sort'] ?? null, function (Builder $query, string $sort) {
                $query->orderBy($sort, $filters['direction'] ?? 'asc');
            }, function (Builder $query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        $roles = $data['roles'] ?? null;
        unset($data['roles']);

        // Rule: Prevent creating a new user as 'owner' if one already exists.
        if (is_array($roles) && in_array('owner', $roles)) {
            if (User::role('owner')->exists()) {
                throw new AppException(
                    userMessage: 'user::exceptions.owner_already_exists',
                    code: 403
                );
            }
        }

        $user = User::create($data);

        if (is_array($roles)) {
            $user->assignRole($roles);
        }

        return $user;
    }

    /**
     * Find a user by ID.
     */
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find a user by username.
     */
    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    /**
     * Update a user's details.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);
        $roles = $data['roles'] ?? null;
        unset($data['roles']);

        // Rule: Prevent removing 'owner' role from the owner account.
        if ($user->hasRole('owner') && (! is_array($roles) || ! in_array('owner', $roles))) {
            throw new AppException(
                userMessage: 'user::exceptions.owner_role_cannot_be_removed',
                code: 403
            );
        }

        // Rule: Prevent assigning 'owner' role if another owner exists.
        if (is_array($roles) && in_array('owner', $roles) && ! $user->hasRole('owner')) {
            if (User::role('owner')->exists()) {
                throw new AppException(
                    userMessage: 'user::exceptions.owner_cannot_be_transferred',
                    code: 403
                );
            }
        }

        // Filter out null/empty password to prevent overwriting.
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        if (is_array($roles)) {
            $user->syncRoles($roles);
        }

        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(string $id): bool
    {
        $user = User::findOrFail($id);

        // Rule: Prevent deleting the owner account.
        if ($user->hasRole('owner')) {
            throw new AppException(
                userMessage: 'user::exceptions.owner_cannot_be_deleted',
                code: 403
            );
        }

        return $user->delete();
    }
}
