<?php

namespace Modules\Shared\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;

trait EloquentQuery
{
    protected Model $model;

    /**
     * The Eloquent query builder instance.
     */
    protected ?Builder $query = null;

    /**
     * The singular name of the record this service manages.
     */
    protected string $recordName = 'record';

    /**
     * Define the columns that can be searched when the `list` method is called.
     * Consuming classes can override this property.
     *
     * @var array<int, string>
     */
    protected array $searchableColumns = [];

    /**
     * Set the Eloquent model instance for the trait.
     * This method effectively replaces the constructor logic of EloquentService.
     */
    protected function setModel(Model $model): void
    {
        $this->model = $model;
        $this->recordName = strtolower(class_basename($model));
    }

    /**
     * Set the query builder instance.
     *
     * @param  (callable(Builder $query))|Builder  $query
     */
    protected function setQuery(callable|Builder $query): void
    {
        $this->query = is_callable($query) ? $query($this->model->newQuery()) : $query;
    }

    /**
     * Get a query builder instance for the model.
     *
     * @param  array<int, string>  $columns  Columns to select.
     */
    public function query(array $columns = ['*']): Builder
    {
        return $this->query ?? $this->model->newQuery()->select($columns);
    }

    /**
     * List records with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria.
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->query($columns);

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $this->applySearchFilter($query, $filters['search']);
        }

        if (isset($filters['sort'])) {
            $query->orderBy($filters['sort'], $filters['direction'] ?? 'asc');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage, $columns);
    }

    /**
     * Apply search filter to the query.
     */
    protected function applySearchFilter(Builder $query, string $search): void
    {
        $searchableColumns = ! empty($this->searchableColumns) ? $this->searchableColumns : ['name'];

        $query->where(function (Builder $query) use ($search, $searchableColumns) {
            $first = true;
            foreach ($searchableColumns as $column) {
                if ($this->model->getConnection()->getSchemaBuilder()->hasColumn($this->model->getTable(), $column)) {
                    if ($first) {
                        $query->where($column, 'like', sprintf('%%%s%%', $search));
                        $first = false;
                    } else {
                        $query->orWhere($column, 'like', sprintf('%%%s%%', $search));
                    }
                }
            }
        });
    }

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data  The data for creating the record.
     */
    public function create(array $data): Model
    {
        try {
            return $this->query()->create($data);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: sprintf('Attempted to create %s with duplicate unique field: %s', $this->recordName, $e->getMessage()),
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: sprintf('Creation of %s failed: %s', $this->recordName, $e->getMessage()),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Find a record by its primary key.
     *
     * @param  mixed  $id  The primary key of the record.
     * @param  array<int, string>  $columns  Columns to retrieve.
     */
    public function find(mixed $id, array $columns = ['*']): ?Model
    {
        /** @var Model|null $record */
        $record = $this->query()->find($id, $columns);

        return $record;
    }

    /**
     * Retrieve the first record matching the query.
     *
     * @param  array<int, string>  $columns
     */
    public function first(array $columns = ['*']): ?Model
    {
        return $this->query()->first($columns);
    }

    /**
     * Check if a record exists based on the given conditions.
     *
     * @param  array<string, mixed>|callable  $where  Conditions for existence check.
     */
    public function exists(array|callable $where = []): bool
    {
        $query = $this->query();

        return empty($where) ? $query->exists() : $query->where($where)->exists();
    }

    /**
     * Update a record's details by its ID.
     *
     * @param  mixed  $id  The primary key of the record.
     * @param  array<string, mixed>  $data  The data for updating the record.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     *
     * @throws RecordNotFoundException If the record is not found.
     * @throws AppException If the update fails due to a database error.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): Model
    {
        /** @var Model $record */
        $record = $this->query()->find($id, $columns);

        if (! $record) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        try {
            $record->update($data);

            return $record;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: sprintf('Attempted to update %s with duplicate unique field: %s', $this->recordName, $e->getMessage()),
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.update_failed',
                replace: ['record' => $this->recordName],
                logMessage: sprintf('Update of %s failed: %s', $this->recordName, $e->getMessage()),
                code: 500,
                previous: $e
            );
        }
    }

    public function updateOrCreate(array $data): Model
    {
        return $this->model->updateOrCreate(['id' => $data['id'] ?? null], $data);
    }

    /**
     * Delete a record by its ID.
     *
     * @param  mixed  $id  The primary key of the record.
     *
     * @throws RecordNotFoundException If the record is not found.
     * @throws AppException If deletion fails due to a foreign key constraint.
     */
    public function delete(mixed $id): bool
    {
        $record = $this->query()->find($id);

        if (! $record) {
            throw new RecordNotFoundException(replace: ['record' => $this->recordName, 'id' => $id]);
        }

        try {
            return $record->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Foreign key constraint violation
                throw new AppException(
                    userMessage: 'shared::exceptions.cannot_delete_associated',
                    replace: ['record' => $this->recordName],
                    logMessage: sprintf('Attempted to delete %s with associated records: %s', $this->recordName, $e->getMessage()),
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.deletion_failed',
                replace: ['record' => $this->recordName],
                logMessage: sprintf('Deletion of %s failed: %s', $this->recordName, $e->getMessage()),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Set the columns that should be used for searching.
     */
    public function setSearchable(string|array ...$columns): void
    {
        $flattenedColumns = Arr::flatten($columns);
        $this->searchableColumns = array_unique($flattenedColumns);
    }
}
