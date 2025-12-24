<?php

namespace Modules\Permission\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Permission\Models\Role;

interface RoleService
{
    /**
     * List roles with filtering and pagination.
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new role.
     */
    public function create(array $data): Role;

    /**
     * Update a role.
     */
    public function update(string|int $id, array $data): Role;

    /**
     * Delete a role.
     */
    public function delete(string|int $id): bool;

    /**
     * Find a role by ID.
     */
    public function findById(string|int $id): ?Role;

    /**
     * Sync permissions to a role.
     */
    public function syncPermissions(string|int $id, array $permissions): Role;
}
