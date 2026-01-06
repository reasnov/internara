<?php

namespace Modules\Department\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DepartmentService
{
    /**
     * List departments with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'sort').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of departments.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Check if a record exists based on the given conditions.
     *
     * @param  array<string, mixed>|callable  $where  Conditions for existence check.
     * @return bool True if a record exists, false otherwise.
     */
    public function exists(array|callable $where = []): bool;
}
