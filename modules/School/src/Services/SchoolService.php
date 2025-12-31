<?php

namespace Modules\School\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\School\Contracts\Services\SchoolService as SchoolServiceContract;
use Modules\School\Models\School;

class SchoolService implements SchoolServiceContract
{
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
