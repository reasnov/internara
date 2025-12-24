<?php

namespace Modules\Permission\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Permission\Models\Permission;

interface PermissionService
{
    /**
     * List permissions with filtering and pagination.
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new permission.
     */
    public function create(array $data): Permission;

    /**
     * Update a permission.
     */
    public function update(string|int $id, array $data): Permission;

    /**
     * Delete a permission.
     */
    public function delete(string|int $id): bool;

    /**
     * Find a permission by ID.
     */
    public function findById(string|int $id): ?Permission;
}
