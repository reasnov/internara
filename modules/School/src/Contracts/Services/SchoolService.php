<?php

namespace Modules\School\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\School\Models\School;

interface SchoolService
{
    /**
     * Retrieve schools based on conditions.
     * Returns a single School model if configured as single record, or a Collection otherwise.
     *
     * @param  array<string, mixed>  $where
     * @param  array<int, string>  $columns
     * @return School|\Illuminate\Support\Collection
     */
    public function get(array $where = [], array $columns = ['*']): School|\Illuminate\Support\Collection;

    /**
     * List schools with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new school.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): School;

    /**
     * Find a school by ID.
     */
    public function findById(string $id): ?School;

    /**
     * Update a school's details.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): School;

    /**
     * Delete a school.
     */
    public function delete(string $id): bool;
}
