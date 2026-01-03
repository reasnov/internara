<?php

namespace Modules\Shared\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Modules\Shared\Exceptions\AppException;

abstract class EloquentService
{
    /**
     * The Eloquent model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * The singular name of the record for translation messages (e.g., 'user', 'department').
     * This should be overridden by child classes if needed, or set dynamically.
     *
     * @var string
     */
    protected string $recordName = 'record'; // Default generic name

    /**
     * EloquentService constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->recordName = strtolower(class_basename($model)); // Default record name from model
    }

    /**
     * List records with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                // Generic search assumes a 'name' field, child classes should override for specific needs.
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
     * @param  array<string, mixed>  $data
     * @return Model The newly created record.
     *
     * @throws AppException If creation fails (e.g., duplicate unique field).
     */
    public function create(array $data): Model
    {
        try {
            return $this->model->create($data);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry SQLSTATE code
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to create '.$this->recordName.' with duplicate unique field. Error: '.$e->getMessage(),
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Creation of '.$this->recordName.' failed due to database error: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Find a record by its primary key.
     */
    public function find(string $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Check if a record exists based on the given conditions.
     */
    public function exists(array|callable $where = []): bool
    {
        $query = $this->model->query();
        return empty($where) ? $query->exists() : $query->where($where)->exists();
    }

    /**
     * Get a new query builder instance for the model.
     */
    public function query(array $columns = ['*']): Builder
    {
        return $this->model->query()->select($columns);
    }

    /**
     * Update a record's details.
     *
     * @param  array<string, mixed>  $data
     * @return Model The updated record.
     *
     * @throws AppException If update fails (e.g., record not found, duplicate unique field).
     */
    public function update(string $id, array $data, array $columns = ['*']): Model
    {
        $record = $this->model->findOrFail($id, $columns); // Will throw ModelNotFoundException if not found

        try {
            $record->update($data);

            return $record;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry SQLSTATE code
                throw new AppException(
                    userMessage: 'shared::exceptions.name_exists', // Using name_exists for generic unique conflict
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to update '.$this->recordName.' with duplicate unique field. Error: '.$e->getMessage(),
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.update_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Update of '.$this->recordName.' failed due to database error: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Delete a record.
     *
     * @return bool True if the record was deleted, false otherwise.
     *
     * @throws AppException If deletion fails (e.g., associated records exist).
     */
    public function delete(string $id, array $columns = ['*']): bool
    {
        $record = $this->model->findOrFail($id, $columns); // Will throw ModelNotFoundException if not found

        try {
            return $record->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Foreign key constraint violation (generic)
                throw new AppException(
                    userMessage: 'shared::exceptions.cannot_delete_associated',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to delete '.$this->recordName.' with associated records. Error: '.$e->getMessage(),
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.deletion_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Deletion of '.$this->recordName.' failed due to database error: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }
}
