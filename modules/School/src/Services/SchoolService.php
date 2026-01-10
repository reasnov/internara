<?php

namespace Modules\School\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Modules\School\Contracts\Services\SchoolService as SchoolServiceContract;
use Modules\School\Models\School;
use Modules\Shared\Concerns\EloquentQuery;
use Modules\Shared\Exceptions\AppException;

class SchoolService implements SchoolServiceContract
{
    use EloquentQuery {
        create as eloquentCreate;
        update as eloquentUpdate;
        updateOrCreate as eloquentUpdateOrCreate;
        first as eloquentFirst;
    }

    /**
     * SchoolService constructor.
     */
    public function __construct(School $model)
    {
        $this->setModel($model);
        $this->setSearchable(['name', 'email']);
    }

    /**
     * Retrieve schools based on conditions.
     * Returns a single School model if configured as single record, or a Collection otherwise.
     *
     * @param  array<string, mixed>  $where  Conditions to filter the query.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return School|\Illuminate\Support\Collection The found school or a collection of schools.
     */
    public function get(array $where = [], array $columns = ['*']): School|\Illuminate\Support\Collection
    {
        $query = $this->model->query();

        foreach ($where as $column => $value) {
            $query->where($column, $value);
        }

        if (config('school.single_record')) {
            return $query->first($columns);
        }

        return $query->get($columns);
    }

    /**
     * List schools with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'sort').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of schools.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->query()->select($columns)
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['sort'] ?? null, function (Builder $query, string $sort) {
                $query->orderBy($sort, $filters['direction'] ?? 'asc');
            }, function (Builder $query) {
                $query->latest();
            })
            ->paginate($perPage);
    }

    /**
     * Create a new school, respecting the single-record configuration.
     *
     * @param  array<string, mixed>  $data  The data for creating the school.
     * @return School The newly created school.
     *
     * @throws AppException If a school already exists and the system is in single-record mode.
     */
    public function create(array $data): School
    {
        try {
            if (config('school.single_record') && $this->model->count() > 0) {
                throw new AppException(
                    userMessage: 'school::exceptions.single_record_exists',
                    code: 409 // Conflict
                );
            }

            /** @var School $school */
            $school = $this->eloquentCreate($data);

            if (isset($data['school_logo_file']) && $data['school_logo_file']) {
                $school->addMedia($data['school_logo_file'])->toMediaCollection('school_logo');
            }

            return $school;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to create school with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'School creation failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    public function update(mixed $id, array $data, array $columns = ['*']): School
    {
        /** @var School $school */
        $school = $this->model->where('id', $id)->firstOrFail();

        $school->update($data);

        if (isset($data['school_logo_file']) && $data['school_logo_file']) {
            $school->clearMediaCollection('school_logo');
            $school->addMedia($data['school_logo_file'])->toMediaCollection('school_logo');
        }

        return $school;
    }

    public function updateOrCreate(array $data): School
    {
        return $this->eloquentUpdateOrCreate($data);
    }

    /**
     * Retrieve the first school record.
     *
     * @param  array<int, string>  $columns
     */
    public function first(array $columns = ['*']): ?School
    {
        return $this->eloquentFirst($columns);
    }
}
