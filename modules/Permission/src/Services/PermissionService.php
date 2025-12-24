<?php

namespace Modules\Permission\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Permission\Contracts\Services\PermissionService as PermissionServiceContract;
use Modules\Permission\Models\Permission;

class PermissionService implements PermissionServiceContract
{
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Permission::query()
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

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(string|int $id, array $data): Permission
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);

        return $permission;
    }

    public function delete(string|int $id): bool
    {
        return Permission::findOrFail($id)->delete();
    }

    public function findById(string|int $id): ?Permission
    {
        return Permission::find($id);
    }
}
