<?php

namespace Modules\User\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException; // Added for exception handling
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
    public function create(array $data): Model
    {
        if ($this->ownerExists()) {
            throw new AppException(
                userMessage: 'shared::exceptions.owner_exists',
                logMessage: 'Attempted to create a second owner account.',
                code: 409
            );
        }

        // Use the Eloquent model directly since OwnerService is specific to User and we need to assign role.
        try {
            /** @var \Modules\User\Models\User $owner */
            $owner = $this->model->newQuery()->create($data); // Directly create the user model

            $owner->assignRole('owner');
            $owner->loadMissing(['roles', 'permissions']); // Load relations after role assignment

            return $owner;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists', // or email_exists, more generic
                    replace: ['record' => $this->recordName],
                    logMessage: sprintf('Attempted to create %s with duplicate unique field: %s', $this->recordName, $e->getMessage()),
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: sprintf('Creation of %s failed: %s', $this->recordName, $e->getMessage()),
                code: 500,
                previous: $e
            );
        }
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
    public function update(mixed $id, array $data, array $columns = ['*']): Model
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

        try {
            $existingOwner->update($data); // Directly update the owner model
            $existingOwner->loadMissing(['roles', 'permissions']); // Load relations after update

            return $existingOwner;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists', // or email_exists, more generic
                    replace: ['record' => $this->recordName],
                    logMessage: sprintf('Attempted to update %s with duplicate unique field: %s', $this->recordName, $e->getMessage()),
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.update_failed',
                replace: ['record' => $this->recordName],
                logMessage: sprintf('Update of %s failed: %s', $this->recordName, $e->getMessage()),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Get the single owner user.
     *
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return \Modules\User\Models\User|null The owner user or null if not found.
     */
    public function get(array $columns = ['*']): ?Model
    {
        /** @var User|null $owner */
        $owner = $this->query($columns)->first();
        if ($owner) {
            $owner->loadMissing(['roles', 'permissions']);
        }

        return $owner;
    }

    /**
     * Check if an owner account already exists.
     */
    protected function ownerExists(): bool
    {
        return $this->query(columns: ['id'])->exists();
    }
}
