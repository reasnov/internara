<?php

namespace Modules\School\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\School;

interface SchoolService
{
    /**
     * Retrieve schools based on conditions.
     *
     * @param  array<string, mixed>  $where  Conditions to filter the query.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return School|\Illuminate\Support\Collection The found school or a collection of schools.
     */
    public function get(array $where = [], array $columns = ['*']): School|\Illuminate\Support\Collection;

    /**
     * Retrieve the first school record.
     *
     * @param  array<int, string>  $columns
     */
    public function first(array $columns = ['*']): ?School;

    /**
     * List schools with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'sort').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of schools.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Create a new school record.
     *
     * @param  array<string, mixed>  $data  The data for creating the school.
     * @return School The newly created school.
     */
    public function create(array $data): School;

    /**
     * Check if a record exists based on the given conditions.
     *
     * @param  array<string, mixed>|callable  $where  Conditions for existence check.
     * @return bool True if a record exists, false otherwise.
     */
    public function exists(array|callable $where = []): bool;

    public function update(mixed $id, array $data, array $columns = ['*']): School;

    public function updateOrCreate(array $data): School;

    public function query(array $columns = ['*']): Builder;

    public function registerFromRelatedModel(Model $model, ?string $foreignKey = null, ?string $ownerKey = null, ?string $relation = null): BelongsTo;
}
