<?php

namespace Modules\School\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\School\Contracts\Services\SchoolService as SchoolServiceContract;
use Modules\School\Models\School;
use Modules\Shared\Exceptions\AppException;

class SchoolService implements SchoolServiceContract
{
    public function __construct()
    {
        //
    }

    /**
     * Retrieve schools based on conditions.
     * Returns a single School model if configured as single record, or a Collection otherwise.
     *
     * @param  array<string, mixed>  $where
     * @param  array<int, string>  $columns
     * @return School|\Illuminate\Support\Collection
     */
    public function get(array $where = [], array $columns = ['*']): School|\Illuminate\Support\Collection
    {
        $query = School::query();

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
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return School::query()
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
     * Create a new school.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): School
    {
        if (config('school.single_record') && School::count() > 0) {
            throw new AppException(
                userMessage: 'school::exceptions.single_record_exists',
                code: 409 // Conflict
            );
        }

        return School::create($data);
    }

    /**
     * Find a school by ID.
     */
    public function findById(string $id): ?School
    {
        return School::find($id);
    }

    /**
     * Update a school's details.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): School
    {
        $school = School::findOrFail($id);
        $school->update($data);

        return $school;
    }

    /**
     * Delete a school.
     */
    public function delete(string $id): bool
    {
        $school = School::findOrFail($id);

        return $school->delete();
    }
}
