<?php

namespace Modules\Permission\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model; // <--- ADDED THIS LINE
use Modules\Permission\Models\Permission;

interface PermissionService
{
    /**
     * List permissions with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'module').
     * @param  int  $perPage  Number of records per page.
     * @return LengthAwarePaginator Paginated list of permissions.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Create a new permission record.
     *
     * @param  array<string, mixed>  $data The data for creating the permission.
     * @return Permission The newly created permission.
     */
    public function create(array $data): Permission;

    /**
     * Update a permission's details by its ID.
     *
     * @param  string  $id  The UUID of the permission.
     * @param  array<string, mixed>  $data  The data for updating the permission.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     * @return Permission The updated permission.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): Model;

    /**
     * Delete a permission by its ID.
     *
     * @param  string  $id  The UUID of the permission.
     * @param  array<int, string>  $columns
     * @return bool True if deletion was successful.
     */
    public function delete(mixed $id): bool;
}
