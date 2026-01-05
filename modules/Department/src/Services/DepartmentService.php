<?php

namespace Modules\Department\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Department\Contracts\Services\DepartmentService as DepartmentServiceContract;
use Modules\Department\Models\Department;
use Modules\Shared\Concerns\EloquentQuery;

class DepartmentService implements DepartmentServiceContract
{
    use EloquentQuery;

    public function __construct(Department $model)
    {
        $this->setModel($model);
    }

    /**
     * List departments with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'sort').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of departments.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->query()->select($columns)
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($filters['sort'] ?? null, function (Builder $query, string $sort) {
                $query->orderBy($sort, $filters['direction'] ?? 'asc');
            }, function (Builder $query) {
                $query->latest();
            })
            ->paginate($perPage);
    }
}
