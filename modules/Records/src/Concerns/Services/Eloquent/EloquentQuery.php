<?php

namespace Modules\Records\Concerns\Services\Eloquent;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
trait EloquentQuery
{
    /**
     * @var TModel
     */
    protected Model $model;

    protected array $searchable = [];

    protected array $sortable = [];

    protected ?Builder $baseQuery = null;

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setBaseQuery(Builder $query): self
    {
        $this->baseQuery = $query;

        return $this;
    }

    public function setSearchable(array $columns = []): self
    {
        $this->searchable = $columns;

        return $this;
    }

    public function setSortable(array $columns = []): self
    {
        $this->sortable = $columns;

        return $this;
    }

    public function paginate(array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query($filters, $columns)->paginate($perPage);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->newQuery()->get($columns);
    }

    public function get(array $filters = [], array $columns = ['*']): Collection
    {
        return $this->query($filters, $columns)->get();
    }

    public function first(array $filters = [], array $columns = ['*']): ?Model
    {
        return $this->query($filters, $columns)->first();
    }

    public function firstOrFail(array $filters = [], array $columns = ['*']): Model
    {
        return $this->query($filters, $columns)->firstOrFail();
    }

    public function find(mixed $id, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->find($id, $columns);
    }

    public function exists(array $filters = []): bool
    {
        return $this->query($filters)->exists();
    }

    public function create(array $data): Model
    {
        $filteredData = $this->filterFillable($data);

        return $this->model->newQuery()->create($filteredData);
    }

    public function update(mixed $id, array $data): Model
    {
        $model = $this->find($id);
        $filteredData = $this->filterFillable($data);
        $model->update($filteredData);

        return $model;
    }

    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->newQuery()->updateOrCreate($attributes, $values);
    }

    public function delete(mixed $id, bool $force = false): bool
    {
        $model = $this->find($id);
        if (! $model) {
            return false;
        }

        return $force ? $model->forceDelete() : $model->delete();
    }

    public function insert(array $data): bool
    {
        return $this->model->newQuery()->insert($data);
    }

    public function upsert(array $values, array|string $uniqueBy, ?array $update = null): int
    {
        return $this->model->newQuery()->upsert($values, $uniqueBy, $update);
    }

    public function destroy(mixed $ids, bool $force = false): int
    {
        if ($force) {
            $count = 0;
            $models = $this->model->newQuery()->whereIn($this->model->getKeyName(), Arr::wrap($ids))->get();
            foreach ($models as $model) {
                if ($model->forceDelete()) {
                    $count++;
                }
            }

            return $count;
        }

        return $this->model->destroy(Arr::wrap($ids));
    }

    public function query(array $filters = [], array $columns = ['*']): Builder
    {
        $query = $this->baseQuery ? clone $this->baseQuery : $this->model->newQuery();

        $query->select($columns);

        $this->applyFilters($query, $filters);

        return $query;
    }

    public function remember(string $cacheKey, mixed $ttl, Closure $callback): mixed
    {
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    protected function applyFilters(Builder &$query, array &$filters): void
    {
        // Sorting logic
        if (isset($filters['sort_by']) && in_array($filters['sort_by'], $this->sortable)) {
            $sortBy = $filters['sort_by'];
            $sortDir = strtolower($filters['sort_dir'] ?? 'asc');

            if (in_array($sortDir, ['asc', 'desc'])) {
                $query->orderBy($sortBy, $sortDir);
            }
            unset($filters['sort_by'], $filters['sort_dir']);
        }

        // Search logic
        if (isset($filters['search']) && ! empty($this->searchable)) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchable as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
            unset($filters['search']);
        }

        // Apply remaining simple where filters
        if (! empty($filters)) {
            $query->where($filters);
        }
    }

    protected function filterFillable(array $data): array
    {
        return Arr::only($data, $this->model->getFillable());
    }
}
