<?php

namespace Modules\Department\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Department\Contracts\Services\DepartmentService as DepartmentServiceContract;
use Modules\Department\Models\Department;
use Modules\School\Contracts\Services\SchoolService;
use Modules\Shared\Concerns\EloquentQuery;

/**
 * @property Department $model
 */
class DepartmentService implements DepartmentServiceContract
{
    use EloquentQuery {
        create as createQuery;
        update as updateQuery;
        updateOrCreate as updateOrCreateQuery;
    }

    public function __construct(Department $model, protected SchoolService $schoolService)
    {
        $this->setModel($model);
        $this->setSearchable(['name', 'school_id']);
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

    public function create(array $data): Department
    {
        $schoolId = $data['school_id'] ?? null;
        unset($data['school_id']);

        $department = $this->createQuery($data);
        $department->changeSchoolId($schoolId);

        $department->refresh();
        $department->loadMissing(['school']);

        return $department;
    }

    public function update(mixed $id, array $data, array $columns = ['*']): Department
    {
        $schoolId = $data['school_id'] ?? null;
        unset($data['school_id']);

        $department = $this->updateQuery($id, $data, $columns);
        $department->changeSchoolId($schoolId);

        $department->refresh();
        $department->loadMissing(['school']);

        return $department;
    }

    public function updateOrCreate(array $data): Department
    {
        $schoolId = $data['school_id'] ?? null;
        unset($data['school_id']);

        $department = $this->updateOrCreateQuery($data);
        $department->changeSchoolId($schoolId);

        $department->refresh();
        $department->loadMissing(['school']);

        return $department;
    }
}
