<?php

namespace Modules\Permission\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Permission\Services\Contracts\RoleService as RoleServiceContract;
use Modules\Permission\Models\Role;
use Modules\Shared\Services\EloquentQuery;

class RoleService extends EloquentQuery implements RoleServiceContract
{
    use EloquentQuery;

    /**
     * RoleService constructor.
     */
    public function __construct(Role $model)
    {
        $this->setModel($model);
    }

    /**
     * List roles with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'module').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of roles.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->query()->select($columns)
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%");
            })
            ->when($filters['module'] ?? null, function (Builder $query, string $module) {
                $query->where('module', $module);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Sync permissions to a role.
     *
     * @param  string  $id  The UUID of the role.
     * @param  array<int, string>  $permissions  An array of permission names to sync.
     * @return Role The role with updated permissions.
     */
    public function syncPermissions(string $id, array $permissions): Role
    {
        /** @var Role $role */
        $role = $this->model->findOrFail($id);
        $role->syncPermissions($permissions);

        return $role;
    }
}
