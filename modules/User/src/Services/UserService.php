<?php

namespace Modules\User\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\Exception\AppException;
use Modules\Exception\RecordNotFoundException;
use Modules\Shared\Services\EloquentQuery;
use Modules\User\Contracts\Services\SuperAdminService;
use Modules\User\Models\User;
use Modules\User\Services\Contracts\UserService as UserServiceContract;

/**
 * @property User $model
 */
class UserService extends EloquentQuery implements UserServiceContract
{
    /**
     * The OwnerService instance.
     */
    protected SuperAdminService $superAdminService;

    /**
     * UserService constructor.
     */
    public function __construct(User $model, SuperAdminService $superAdminService)
    {
        $this->setModel($model);
        $this->setSearchable('name', 'email', 'username');
        $this->superAdminService = $superAdminService;
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
        return parent::paginate($filters, $perPage, $columns);
    }

    /**
     * Create a new user with specific business rules.
     *
     * @param  array<string, mixed>  $data  The data for creating the user, optionally including 'roles'.
     * @return User The newly created user.
     *
     * @throws AppException If the 'super-admin' role is assigned and a SuperAdmin already exists.
     */
    public function create(array $data): User
    {
        $roles = $data['roles'] ?? null;
        $originalRoles = $roles;
        unset($data['roles']);

        if ($roles !== null) {
            $roles = Arr::wrap($roles);
            if (in_array('super-admin', $roles)) {

                // Use for SuperAdmin account setup
                if (! setting('app_installed', true)) {
                    return $this->superAdminService->save($data);
                }

                if ($this->superAdminService->exists()) {
                    throw new AppException(
                        userMessage: 'user::exceptions.super_admin_already_exists',
                        code: 403
                    );
                }

                return $this->superAdminService->create($data);
            }
        }

        $user = parent::create($data);
        $this->handleUserAvatar($user, $data['avatar_file'] ?? null);

        if ($originalRoles !== null) {
            $user->assignRole($originalRoles);
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
     * @throws AppException If attempting to modify SuperAdmin roles incorrectly.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User
    {
        /** @var User|null $user */
        $user = $this->find($id);

        if (! $user) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        $roles = $data['roles'] ?? null;
        $originalRoles = $roles; // Store original roles for later assignment if not super-admin
        unset($data['roles']);

        if ($user->hasRole('super-admin')) {
            return $this->superAdminService->update($id, $data, $columns);
        }

        if ($originalRoles !== null) {
            $roles = Arr::wrap($originalRoles);
            if (in_array('super-admin', $roles) && ! $user->hasRole('super-admin')) {
                if ($this->superAdminService->exists()) {
                    throw new AppException(
                        userMessage: 'user::exceptions.super_admin_cannot_be_transferred',
                        code: 403
                    );
                }
            }
        }

        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $updatedUser = parent::update($id, $data, $columns);
        $this->handleUserAvatar($updatedUser, $data['avatar_file'] ?? null);

        if ($originalRoles !== null) {
            $updatedUser->syncRoles($originalRoles);
        }

        return $updatedUser;
    }

    /**
     * Delete a user with specific business rules.
     *
     * @param  string  $id  The UUID of the user to delete.
     * @return bool True if deletion was successful.
     *
     * @throws AppException If attempting to delete the SuperAdmin account.
     */
    public function delete(mixed $id, bool $force = false): bool
    {
        $user = $this->find($id);

        if (! $user) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        if ($user->hasRole('super-admin')) {
            return $this->superAdminService->delete($id, $force);
        }

        return parent::delete($id, $force);
    }

    protected function handleUserAvatar(User &$user, UploadedFile|string|null $avatar = null): bool
    {
        return isset($avatar) ? $user->changeAvatar($avatar) : false;
    }
}
