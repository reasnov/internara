<?php

namespace Modules\Department\Services;

use Modules\Department\Models\Department;
use Modules\School\Services\Contracts\SchoolService;
use Modules\Shared\Services\Concerns\EloquentQuery;

/**
 * @property Department $model
 */
class DepartmentService implements Contracts\DepartmentService
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

    public function update(mixed $id, array $data): Department
    {
        $schoolId = $data['school_id'] ?? null;
        unset($data['school_id']);

        $department = $this->updateQuery($id, $data);
        $department->changeSchoolId($schoolId);

        $department->refresh();
        $department->loadMissing(['school']);

        return $department;
    }

    public function updateOrCreate(array $attributes, array $values = []): Department
    {
        $schoolId = $attributes['school_id'] ?? null;
        unset($attributes['school_id']);

        $department = $this->updateOrCreateQuery($attributes, $values);
        $department->changeSchoolId($schoolId);

        $department->refresh();
        $department->loadMissing(['school']);

        return $department;
    }
}
