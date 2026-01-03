<?php

namespace Modules\Department\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Department\Models\Department;
use Modules\Shared\Exceptions\AppException;

interface DepartmentService
{
    /**
     * List departments with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new department.
     *
     * @param  array<string, mixed>  $data
     * @return Department The newly created department.
     *
     * @throws AppException If creation fails (e.g., duplicate name).
     */
    public function create(array $data): Department;

    /**
     * Find a department by ID.
     */
    public function findById(string $id): ?Department;

    /**
     * Update a department's details.
     *
     * @param  array<string, mixed>  $data
     * @return Department The updated department.
     *
     * @throws AppException If update fails (e.g., department not found, duplicate name).
     */
    public function update(string $id, array $data): Department;

    /**
     * Delete a department.
     *
     * @return bool True if the department was deleted, false otherwise.
     *
     * @throws AppException If deletion fails (e.g., department not found, associated records exist).
     */
    public function delete(string $id): bool;
}