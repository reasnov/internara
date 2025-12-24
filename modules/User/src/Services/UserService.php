<?php

namespace Modules\User\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\User\Contracts\Services\UserService as UserServiceContract;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
    /**
     * List users with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     * @param  int  $perPage
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
        // We do not manually hash the password here because
        // the User model has 'password' => 'hashed' in casts().
        return User::create($data);
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

        // Filter out null/empty password to prevent overwriting with empty string
        // if the user intended to keep the current password.
        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(string $id): bool
    {
        $user = User::findOrFail($id);

        return $user->delete();
    }
}
