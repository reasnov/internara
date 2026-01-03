<?php

namespace Modules\Department\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Modules\Department\Contracts\Services\DepartmentService as DepartmentServiceContract;
use Modules\Department\Models\Department;
use Modules\Shared\Exceptions\AppException;

class DepartmentService implements DepartmentServiceContract
{
    /**
     * List departments with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Department::query()
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

    /**
     * Create a new department.
     *
     * @param  array<string, mixed>  $data
     * @return Department The newly created department.
     *
     * @throws AppException If creation fails (e.g., duplicate name).
     */
    public function create(array $data): Department
    {
        try {
            return Department::create($data);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry SQLSTATE code
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => 'jurusan'],
                    logMessage: 'Attempted to create department with duplicate name: '.$data['name'],
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => 'jurusan'],
                logMessage: 'Department creation failed due to database error: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Find a department by ID.
     */
    public function findById(string $id): ?Department
    {
        return Department::find($id);
    }

    /**
     * Update a department's details.
     *
     * @param  array<string, mixed>  $data
     * @return Department The updated department.
     *
     * @throws AppException If update fails (e.g., department not found, duplicate name).
     */
    public function update(string $id, array $data): Department
    {
        $department = Department::findOrFail($id);

        try {
            $department->update($data);

            return $department;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry SQLSTATE code
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => 'jurusan'],
                    logMessage: 'Attempted to update department with duplicate name: '.$data['name'],
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.update_failed',
                replace: ['record' => 'jurusan'],
                logMessage: 'Department update failed due to database error: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Delete a department.
     *
     * @return bool True if the department was deleted, false otherwise.
     *
     * @throws AppException If deletion fails (e.g., department not found, associated records exist).
     */
    public function delete(string $id): bool
    {
        $department = Department::findOrFail($id);

        // TODO: Add logic to prevent deletion if associated records exist (e.g., users, programs)
        // For example:
        // if ($department->users()->exists()) {
        //     throw new AppException(
        //         userMessage: 'department::exceptions.has_associated_users',
        //         code: 409
        //     );
        // }

        try {
            return $department->delete();
        } catch (QueryException $e) {
            // Check for foreign key constraint violation (SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                throw new AppException(
                    userMessage: 'shared::exceptions.cannot_delete_associated',
                    replace: ['record' => 'jurusan'],
                    logMessage: 'Attempted to delete department with associated records: '.$department->name,
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.deletion_failed',
                replace: ['record' => 'jurusan'],
                logMessage: 'Department deletion failed due to database error: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }
}