<?php

namespace Modules\User\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Modules\Shared\Concerns\EloquentQuery;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Contracts\Services\UserService as UserServiceContract;
use Modules\User\Models\User;

class UserService implements UserServiceContract
{
    use EloquentQuery {
        list as eloquentList;
        create as eloquentCreate;
        update as eloquentUpdate;
        delete as eloquentDelete;
    }

    /**
     * UserService constructor.
     */
    public function __construct(User $userModel)
    {
        $this->setModel($userModel);
        $this->setSearchable('name', 'email', 'username');
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
        return $this->eloquentList($filters, $perPage, $columns);
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
        $roles = $data['roles'] ?? null;
        unset($data['roles']);

        if ($roles !== null) {
            $roles = Arr::wrap($roles);
            if (in_array('owner', $roles)) {
                if ($this->model->role('owner')->exists()) {
                    throw new AppException(
                        userMessage: 'user::exceptions.owner_already_exists',
                        code: 403
                    );
                }
            }
        }

        /** @var User $user */
        $user = $this->eloquentCreate($data);

        if ($roles !== null) {
            $user->assignRole($roles);
        }

        return $user;
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
        /** @var User|null $user */
        $user = $this->find($id);

        if (! $user) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        $roles = $data['roles'] ?? null;
        unset($data['roles']);

        if ($roles !== null) {
            $roles = Arr::wrap($roles);
        }

        if ($user->hasRole('owner') && ($roles === null || ! in_array('owner', $roles))) {
            throw new AppException(
                userMessage: 'user::exceptions.owner_role_cannot_be_removed',
                code: 403
            );
        }

        if (($roles !== null) && in_array('owner', $roles) && ! $user->hasRole('owner')) {
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

        $updatedUser = $this->eloquentUpdate($id, $data, $columns);

        if ($roles !== null) {
            $updatedUser->syncRoles($roles);
        }

        return $updatedUser;
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
        $user = $this->find($id);

        if (! $user) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        if ($user->hasRole('owner')) {
            throw new AppException(
                userMessage: 'user::exceptions.owner_cannot_be_deleted',
                code: 403
            );
        }

        return $this->eloquentDelete($id);
    }
}
