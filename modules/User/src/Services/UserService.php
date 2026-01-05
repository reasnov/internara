<?php

namespace Modules\User\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Modules\Shared\Concerns\EloquentQuery;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\UserService as UserServiceContract;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
    use EloquentQuery;

    /**
     * UserService constructor.
     */
    public function __construct(User $userModel)
    {
        $this->setModel($userModel);
    }

    /**
     * List users with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'sort', 'direction').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of users.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->query()
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
            ->paginate($perPage, $columns);
    }

    /**
     * Create a new user with specific business rules.
     *
     * @param  array<string, mixed>  $data  The data for creating the user, optionally including 'roles'.
     * @return User The newly created user.
     *
     * @throws AppException If the 'owner' role is assigned and an owner already exists.
     */
    public function create(array $data): User
    {
        try {
            $roles = $data['roles'] ?? null;
            unset($data['roles']);

            if (is_array($roles) && in_array('owner', $roles)) {
                if ($this->model->role('owner')->exists()) {
                    throw new AppException(
                        userMessage: 'user::exceptions.owner_already_exists',
                        code: 403
                    );
                }
            }

            /** @var User $user */
            $user = $this->model->create($data);

            if (is_array($roles)) {
                $user->assignRole($roles);
            }

            return $user;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to create user with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'User creation failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Find a user by their email address.
     *
     * @param  string  $email  The email address to search for.
     * @return User|null The found user or null.
     */
    public function findByEmail(string $email): ?User
    {
        /** @var User|null */
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find a user by their username.
     *
     * @param  string  $username  The username to search for.
     * @return User|null The found user or null.
     */
    public function findByUsername(string $username): ?User
    {
        /** @var User|null */
        return $this->model->where('username', $username)->first();
    }

    /**
     * Update a user's details with specific business rules.
     *
     * @param  string  $id  The UUID of the user to update.
     * @param  array<string, mixed>  $data  The data for updating the user.
     * @return User The updated user.
     *
     * @throws AppException If attempting to modify owner roles incorrectly.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User
    {
        /** @var User $user */
        $user = $this->model->findOrFail($id);

        try {
            $roles = $data['roles'] ?? null;
            unset($data['roles']);

            if ($user->hasRole('owner') && (! is_array($roles) || ! in_array('owner', $roles))) {
                throw new AppException(
                    userMessage: 'user::exceptions.owner_role_cannot_be_removed',
                    code: 403
                );
            }

            if (is_array($roles) && in_array('owner', $roles) && ! $user->hasRole('owner')) {
                if ($this->model->role('owner')->exists()) {
                    throw new AppException(
                        userMessage: 'user::exceptions.owner_cannot_be_transferred',
                        code: 403
                    );
                }
            }

            if (array_key_exists('password', $data) && empty($data['password'])) {
                unset($data['password']);
            }

            $user->update($data);

            if (is_array($roles)) {
                $user->syncRoles($roles);
            }

            return $user;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to update user with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.update_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'User update failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Delete a user with specific business rules.
     *
     * @param  string  $id  The UUID of the user to delete.
     * @return bool True if deletion was successful.
     *
     * @throws AppException If attempting to delete the owner account.
     */
    public function delete(mixed $id): bool
    {
        /** @var User $user */
        $user = $this->model->findOrFail($id);

        try {
            if ($user->hasRole('owner')) {
                throw new AppException(
                    userMessage: 'user::exceptions.owner_cannot_be_deleted',
                    code: 403
                );
            }

            return $user->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Foreign key constraint
                throw new AppException(
                    userMessage: 'shared::exceptions.cannot_delete_associated',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to delete user with associated records.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.deletion_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'User deletion failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }
}
