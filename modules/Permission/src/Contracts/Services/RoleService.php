<?php

namespace Modules\Permission\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Permission\Models\Role;

interface RoleService
{
    /**
     * List roles with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'module').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of roles.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Sync permissions to a role.
     *
     * @param  string  $id  The UUID of the role.
     * @param  array<int, string>  $permissions  An array of permission names to sync.
     * @return Role The role with updated permissions.
     */
    public function syncPermissions(string $id, array $permissions): Role;
}
