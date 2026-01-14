<?php

namespace Modules\User\Services\Contracts;

use Modules\User\Models\User;

interface SuperAdminService
{
    /**
     * Create a new SuperAdmin user.
     *
     * @param  array<string, mixed>  $data  The data for creating the SuperAdmin user.
     * @return \Modules\User\Models\User The newly created SuperAdmin user.
     */
    public function create(array $data): User;

    /**
     * Update the existing SuperAdmin user.
     *
     * @param  mixed  $id  The primary key of the SuperAdmin user.
     * @param  array<string, mixed>  $data  The data for updating the SuperAdmin user.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     * @return \Modules\User\Models\User The updated SuperAdmin user.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User;

    /**
     * Update an existing SuperAdmin or create a new one if not found.
     *
     * @param  array<string, mixed>  $data  The data for creating or updating the SuperAdmin.
     * @return \Modules\User\Models\User The created or updated SuperAdmin user.
     */
    public function save(array $attributes, array $values = []): User;

    /**
     * Delete a SuperAdmin user.
     *
     * @param  mixed  $id  The primary key of the SuperAdmin user.
     * @return bool True if deletion was successful.
     */
    public function delete(mixed $id, bool $force = false): bool;

    /**
     * Get the single SuperAdmin user.
     *
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return \Modules\User\Models\User|null The SuperAdmin user or null if not found.
     */
    public function getSuperAdmin(array $columns = ['*']): ?User;

    /**
     * Check if a SuperAdmin account already exists.
     *
     * @return bool True if a SuperAdmin account exists, false otherwise.
     */
    public function exists(): bool;
}
