<?php

namespace Modules\User\Services\Contracts;

use Modules\User\Models\User;

interface OwnerService
{
    /**
     * Create a new owner user.
     *
     * @param  array<string, mixed>  $data  The data for creating the owner user.
     * @return \Modules\User\Models\User The newly created owner user.
     */
    public function create(array $data): User;

    /**
     * Update the existing owner user.
     *
     * @param  mixed  $id  The primary key of the owner user.
     * @param  array<string, mixed>  $data  The data for updating the owner user.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     * @return \Modules\User\Models\User The updated owner user.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User;

    /**
     * Update an existing owner or create a new one if not found.
     *
     * @param  array<string, mixed>  $data  The data for creating or updating the owner.
     * @return \Modules\User\Models\User The created or updated owner user.
     */
    public function save(array $attributes, array $values = []): User;

    /**
     * Delete an owner user.
     *
     * @param  mixed  $id  The primary key of the owner user.
     * @return bool True if deletion was successful.
     */
    public function delete(mixed $id, bool $force = false): bool;

    /**
     * Get the single owner user.
     *
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return \Modules\User\Models\User|null The owner user or null if not found.
     */
    public function getOwner(array $columns = ['*']): ?User;

    /**
     * Check if an owner account already exists.
     *
     * @return bool True if an owner account exists, false otherwise.
     */
    public function exists(): bool;
}
