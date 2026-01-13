<?php

namespace Modules\Permission\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Modules\Permission\Models\Permission;
use Modules\Permission\Services\Contracts\PermissionService as PermissionServiceContract;
use Modules\Shared\Services\EloquentQuery;

class PermissionService extends EloquentQuery implements PermissionServiceContract
{
    use EloquentQuery;

    public function __construct(Permission $model)
    {
        $this->setModel($model);
    }

    /**
     * List permissions with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'module', 'sort').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of permissions.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {

        return $this->model->query()->select($columns) // This line is correct in the existing list method
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%");
            })
            ->when($filters['module'] ?? null, function (Builder $query, string $module) {
                $query->where('module', $module);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new permission record.
     * This method overrides the one from EloquentQuery trait to match the contract.
     *
     * @param  array<string, mixed>  $data  The data for creating the permission.
     * @return Permission The newly created permission.
     *
     * @throws \Modules\Exceptions\AppException If creation fails due to a database error.
     */
    public function create(array $data): Permission
    {
        try {
            /** @var Permission $permission */
            $permission = $this->model->create($data);

            return $permission;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new AppException(
                    userMessage: 'records::exceptions.unique_violation',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to create '.$this->recordName.' with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'records::exceptions.creation_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Creation of '.$this->recordName.' failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Update a permission's details by its ID.
     * This method overrides the one from EloquentQuery trait to match the contract.
     *
     * @param  mixed  $id  The UUID of the permission.
     * @param  array<string, mixed>  $data  The data for updating the permission.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     * @return Permission The updated permission.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record is not found.
     * @throws \Modules\Exceptions\AppException If the update fails due to a database error.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): Permission
    {
        /** @var Permission $permission */
        $permission = $this->model->findOrFail($id, $columns);
        try {
            $permission->update($data);

            return $permission;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry
                throw new AppException(
                    userMessage: 'records::exceptions.unique_violation',
                    replace: ['record' => $this->recordName],
                    logMessage: 'Attempted to update '.$this->recordName.' with duplicate unique field.',
                    code: 409,
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'records::exceptions.update_failed',
                replace: ['record' => $this->recordName],
                logMessage: 'Update of '.$this->recordName.' failed: '.$e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }
}
