<?php

namespace Modules\Permission\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Permission\Contracts\Services\RoleService as RoleServiceContract;
use Modules\Permission\Models\Role;

class RoleService implements RoleServiceContract
{
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Role::query()
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

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(string|int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update($data);

        return $role;
    }

    public function delete(string|int $id): bool
    {
        return Role::findOrFail($id)->delete();
    }

    public function findById(string|int $id): ?Role
    {
        return Role::find($id);
    }

    public function syncPermissions(string|int $id, array $permissions): Role
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($permissions);

        return $role;
    }
}
