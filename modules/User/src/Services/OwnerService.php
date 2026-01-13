<?php

namespace Modules\User\Services;

use Illuminate\Http\UploadedFile;
use Modules\Exception\AppException;
use Modules\Exception\RecordNotFoundException;
use Modules\Shared\Services\EloquentQuery;
use Modules\User\Models\User;

/**
 * Service to manage owner-related operations.
 *
 * @property User $model
 *
 * @method \Illuminate\Database\Eloquent\Builder<User> query(array $columns = ['*'])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator list(array $filters = [], int $perPage = 10, array $columns = ['*'])
 * @method \Modules\User\Models\User create(array $data)
 * @method \Modules\User\Models\User|null find(mixed $id, array $columns = ['*'])
 * @method bool exists(array|callable $where = [])
 * @method \Modules\User\Models\User update(mixed $id, array $data, array $columns = ['*'])
 * @method bool delete(mixed $id, bool $force = false)
 */
class OwnerService extends EloquentQuery implements Contracts\OwnerService
{
    public function __construct(User $user)
    {
        $this->setModel($user);
        $this->setBaseQuery($user->owner());
        $this->setSearchable(['name', 'email', 'username']);
    }

    /**
     * Create a new owner user.
     *
     * @param  array<string, mixed>  $data  The data for creating the owner user.
     * @return \Modules\User\Models\User The newly created owner user.
     *
     * @throws \Modules\Exception\AppException If an owner already exists or creation fails.
     */
    public function create(array $data): User
    {
        if ($this->exists()) {
            throw new AppException(
                userMessage: 'user::exceptions.owner_exists',
                logMessage: 'Attempted to create a second owner account.',
                code: 409
            );
        }

        $owner = parent::create($data);

        $this->handleOwnerAvatar($owner, $data['avatar_file'] ?? null);

        $owner->assignRole('owner');
        $owner->loadMissing(['roles', 'permissions']);

        return $owner;
    }

    /**
     * Update the existing owner user.
     *
     * @param  mixed  $id  The primary key of the owner user.
     * @param  array<string, mixed>  $data  The data for updating the owner user.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     * @return \Modules\User\Models\User The updated owner user.
     *
     * @throws \Modules\Exception\RecordNotFoundException If no owner exists or the provided ID does not match the owner.
     * @throws \Modules\Exception\AppException If the update fails.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User
    {
        /** @var User|null $existingOwner */
        $existingOwner = $this->query(columns: ['id'])->first(); // Use the owner scope to get the single owner

        if (! $existingOwner) { // No owner exists at all
            throw new RecordNotFoundException(
                userMessage: 'user::exceptions.owner_not_found',
                logMessage: 'Attempted to update owner, but no owner account exists.',
                code: 404
            );
        }

        if ($existingOwner->id !== $id) {
            throw new RecordNotFoundException(
                userMessage: 'user::exceptions.owner_not_found',
                replace: ['id' => $id],
                logMessage: sprintf('Attempted to update user with ID %s, but it is not the owner account (actual owner ID: %s).', $id, $existingOwner->id),
                code: 404
            );
        }

        // Remove any role assignment from data to prevent changing owner status
        unset($data['roles']);
        unset($data['role']);

        $updatedOwner = parent::update($existingOwner->id, $data, $columns);

        $this->handleOwnerAvatar($updatedOwner, $data['avatar_file'] ?? null);

        $updatedOwner->loadMissing(['roles', 'permissions']);

        return $updatedOwner;
    }

    /**
     * Update an existing owner or create a new one if not found.
     *
     * @param  array<string, mixed>  $data  The data for creating or updating the owner.
     * @return \Modules\User\Models\User The created or updated owner user.
     */
    public function save(array $attributes, array $values = []): User
    {
        $owner = parent::save($attributes, $values);

        $allData = array_merge($attributes, $values);
        $this->handleOwnerAvatar($owner, $allData['avatar_file'] ?? null);

        $owner->assignRole('owner');
        $owner->loadMissing(['roles', 'permissions']);

        return $owner;
    }

    public function delete(mixed $id, bool $force = false): bool
    {
        /** @var User|null $owner */
        $owner = $this->find($id);

        if (! $owner) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        throw new AppException(
            userMessage: 'user::exceptions.owner_cannot_be_deleted',
            code: 403
        );
    }

    /**
     * Get the single owner user.
     *
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return \Modules\User\Models\User|null The owner user or null if not found.
     */
    public function getOwner(array $columns = ['*']): ?User
    {
        /** @var User|null $owner */
        $owner = $this->query($columns)->first();
        if ($owner) {
            $owner->loadMissing(['roles', 'permissions']);
        }

        return $owner;
    }

    /**
     * Getting a collection of owners is not allowed.
     *
     * @throws \Modules\Exception\AppException
     */
    public function get(array $filters = [], array $columns = ['*']): \Illuminate\Support\Collection
    {
        throw new AppException(
            userMessage: 'user::exceptions.cannot_get_multiple_owners',
            logMessage: 'Attempted to get a collection of owners, which is not allowed.',
            code: 405 // Method Not Allowed
        );
    }

    protected function handleOwnerAvatar(User &$owner, UploadedFile|string|null $avatar = null): bool
    {
        return isset($avatar) ? $owner->changeAvatar($avatar) : false;
    }
}
