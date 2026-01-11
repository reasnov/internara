<?php

namespace Modules\Records\Contracts\Services\Eloquent;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface EloquentService
{
    /**
     * Retrieve paginated records with optional filters.
     *
     * @param  array  $filters
     * @param  int  $perPage
     * @param  array<int, string>  $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Retrieve all records.
     *
     * @param  array<int, string>  $columns
     * @return \Illuminate\Support\Collection<int, TModel>
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Retrieve records based on a set of filters.
     *
     * @param  array  $filters
     * @param  array<int, string>  $columns
     * @return \Illuminate\Support\Collection<int, TModel>
     */
    public function get(array $filters = [], array $columns = ['*']): Collection;

    /**
     * Retrieve the first record matching the filters.
     *
     * @param  array  $filters
     * @param  array<int, string>  $columns
     * @return TModel|null
     */
    public function first(array $filters = [], array $columns = ['*']): ?Model;

    /**
     * Retrieve the first record matching the filters or fail.
     *
     * @param  array  $filters
     * @param  array<int, string>  $columns
     * @return TModel
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstOrFail(array $filters = [], array $columns = ['*']): Model;

    /**
     * Find a record by its primary key.
     *
     * @param  mixed  $id
     * @param  array<int, string>  $columns
     * @return TModel|null
     */
    public function find(mixed $id, array $columns = ['*']): ?Model;

    /**
     * Check if any records exist matching the filters.
     *
     * @param  array  $filters
     * @return bool
     */
    public function exists(array $filters = []): bool;

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function create(array $data): Model;

    /**
     * Update a record by its primary key.
     *
     * @param  mixed  $id
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function update(mixed $id, array $data): Model;

    /**
     * Update an existing record or create a new one.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Delete a record by its primary key.
     *
     * @param  mixed  $id
     * @param  bool  $force
     * @return bool
     */
    public function delete(mixed $id, bool $force = false): bool;

    /**
     * Perform a bulk insert operation.
     *
     * @param  array<int, array<string, mixed>>  $data
     * @return bool
     */
    public function insert(array $data): bool;

    /**
     * Perform a bulk "upsert" operation.
     *
     * @param  array<int, array<string, mixed>>  $values
     * @param  array<int, string>|string  $uniqueBy
     * @param  array<int, string>|null  $update
     * @return int
     */
    public function upsert(array $values, array|string $uniqueBy, ?array $update = null): int;

    /**
     * Destroy multiple records by their primary keys.
     *
     * @param  \Illuminate\Support\Collection<int, mixed>|array<int, mixed>|mixed  $ids
     * @param  bool  $force
     * @return int
     */
    public function destroy(mixed $ids, bool $force = false): int;

    /**
     * Get a new query builder instance.
     *
     * @param  array  $filters
     * @param  array<int, string>  $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(array $filters = [], array $columns = ['*']): Builder;

    /**
     * Retrieve an item from the cache, or execute the given callback and cache the result.
     *
     * @param  string  $cacheKey
     * @param  \DateTimeInterface|\DateInterval|int  $ttl
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember(string $cacheKey, mixed $ttl, Closure $callback): mixed;
}
