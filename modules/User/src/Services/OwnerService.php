<?php

namespace Modules\User\Services;

// Added for exception handling
use Modules\Shared\Concerns\EloquentQuery;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Contracts\Services\OwnerService as OwnerServiceContract;
use Modules\User\Models\User;

/**
 * Service to manage owner-related operations.
 *
 * @method \Illuminate\Database\Eloquent\Builder<User> query(array $columns = ['*'])
 * @method \Illuminate\Contracts\Pagination\LengthAwarePaginator list(array $filters = [], int $perPage = 10, array $columns = ['*'])
 * @method \Modules\User\Models\User create(array $data)
 * @method \Modules\User\Models\User|null find(mixed $id, array $columns = ['*'])
 * @method bool exists(array|callable $where = [])
 * @method \Modules\User\Models\User update(mixed $id, array $data, array $columns = ['*'])
 * @method bool delete(mixed $id)
 * @method \Modules\User\Models\User|null get()
 */
class OwnerService implements OwnerServiceContract
{
    use EloquentQuery {
        create as eloquentCreate;
        update as eloquentUpdate;
        delete as eloquentDelete;
    }

    public function __construct(User $user)
    {
        $this->setModel($user);
        $this->setQuery($user->owner());
        $this->setSearchable(['name', 'email', 'username']);
    }

    /**
     * Create a new owner user.
     *
     * @param  array<string, mixed>  $data  The data for creating the owner user.
     * @return \Modules\User\Models\User The newly created owner user.
     *
     * @throws \Modules\Shared\Exceptions\AppException If an owner already exists or creation fails.
     */
    public function create(array $data): User
    {
        if ($this->exists()) {
            throw new AppException(
                userMessage: 'shared::exceptions.owner_exists',
                logMessage: 'Attempted to create a second owner account.',
                code: 409
            );
        }

        /** @var \Modules\User\Models\User $owner */
        $owner = $this->eloquentCreate($data);

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
     * @throws \Modules\Shared\Exceptions\RecordNotFoundException If no owner exists or the provided ID does not match the owner.
     * @throws \Modules\Shared\Exceptions\AppException If the update fails.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User
    {
        /** @var User|null $existingOwner */
        $existingOwner = $this->query(columns: ['id'])->first(); // Use the owner scope to get the single owner

        if (! $existingOwner) { // No owner exists at all
            throw new RecordNotFoundException(
                userMessage: 'shared::exceptions.owner_not_found',
                logMessage: 'Attempted to update owner, but no owner account exists.',
                code: 404
            );
        }

        if ($existingOwner->id !== $id) { // The provided ID is not the actual owner's ID
            throw new RecordNotFoundException(
                userMessage: 'shared::exceptions.owner_not_found',
                replace: ['id' => $id],
                logMessage: sprintf('Attempted to update user with ID %s, but it is not the owner account (actual owner ID: %s).', $id, $existingOwner->id),
                code: 404
            );
        }

        // Remove any role assignment from data to prevent changing owner status
        unset($data['roles']);
        unset($data['role']);

        /** @var \Modules\User\Models\User $updatedOwner */
        $updatedOwner = $this->eloquentUpdate($existingOwner->id, $data, $columns);
        $updatedOwner->loadMissing(['roles', 'permissions']);

        return $updatedOwner;
    }

    /**
     * Update an existing owner or create a new one if not found.
     *
     * @param  array<string, mixed>  $data  The data for creating or updating the owner.
     * @return \Modules\User\Models\User The created or updated owner user.
     */
    public function updateOrCreate(array $data): User
    {
        $keyName = $this->model->getKeyName();
        $id = $this->query()->first()?->$keyName;

        $owner = $this->model->updateOrCreate([$keyName => $id], $data);
        $owner->assignRole('owner');
        $owner->loadMissing(['roles', 'permissions']);

        return $owner;
    }

    public function delete(mixed $id): bool
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
    public function get(array $columns = ['*']): ?User
    {
        /** @var User|null $owner */
        $owner = $this->query($columns)->first();
        if ($owner) {
            $owner->loadMissing(['roles', 'permissions']);
        }

        return $owner;
    }
}
