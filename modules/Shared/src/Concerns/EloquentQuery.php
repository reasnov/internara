<?php

namespace Modules\Shared\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Modules\Shared\Exceptions\AppException;

trait EloquentQuery
{
    protected Model $model;

    protected string $recordName = 'record';

    /**
     * Set the Eloquent model instance for the trait.
     * This method effectively replaces the constructor logic of EloquentService.
     *
     * @param  Model  $model  The Eloquent model instance.
     */
    protected function setModel(Model $model): void
    {
        $this->model = $model;
        $this->recordName = strtolower(class_basename($model));
    }

    /**
     * Get a new query builder instance for the model.
     *
     * @param  array<int, string>  $columns  Columns to select.
     * @return Builder The Eloquent query builder.
     */
    public function query(array $columns = ['*']): Builder
    {
        return $this->query($columns)->select($columns);
    }

    /**
     * List records with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria.
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of records.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query($columns)
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                if ($this->model->getConnection()->getSchemaBuilder()->hasColumn($this->model->getTable(), 'name')) {
                    $query->where('name', 'like', "%{$search}%");
                }
            })
            ->when($filters['sort'] ?? null, function (Builder $query, string $sort) {
                $query->orderBy($sort, $filters['direction'] ?? 'asc');
            }, function (Builder $query) {
                $query->latest();
            })
            ->paginate($perPage, $columns);
    }

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data  The data for creating the record.
     * @param  array  $columns  Columns to retrieve after creation.
     * @return Model The newly created record.
     *
     * @throws AppException If creation fails due to a database error.
     */
    public function create(array $data, array $columns = ['*']): Model
    {
        try {
            /** @var Model $record */
            $record = $this->model->create($data);

            return $this->model->findOrFail($record->getKey(), $columns);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to create '.$this->recordName.' with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Creation of '.$this->recordName.' failed: '.$e->getMessage(),
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
     * @return Model|null The found record or null if not found.
     */
    public function find(mixed $id, array $columns = ['*']): ?Model
    {
        /** @var Model $record */
        $record = $this->model->find($id, $columns);

        return $record;
    }

    /**
     * Check if a record exists based on the given conditions.
     *
     * @param  array<string, mixed>|callable  $where  Conditions for existence check.
     * @return bool True if a record exists, false otherwise.
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
     * @return Model The updated record.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record is not found.
     * @throws AppException If the update fails due to a database error.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): Model
    {
        /** @var Model $record */
        $record = $this->model->findOrFail($id, $columns);

        try {
            $record->update($data);

            return $record;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to update '.$this->recordName.' with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.update_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Update of '.$this->recordName.' failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Delete a record by its ID.
     *
     * @param  mixed  $id  The primary key of the record.
     * @return bool True if the record was deleted.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record is not found.
     * @throws AppException If deletion fails due to a foreign key constraint.
     */
    public function delete(mixed $id): bool
    {
        $record = $this->model->findOrFail($id);

        try {
            return $record->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Foreign key constraint violation
                throw new AppException(
                    userMessage: 'shared::exceptions.cannot_delete_associated',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to delete '.$this->recordName.' with associated records.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.deletion_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Deletion of '.$this->recordName.' failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }
}
