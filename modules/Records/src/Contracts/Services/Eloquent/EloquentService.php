<?php

namespace Modules\Records\Contracts\Services\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EloquentService
{
    public function lists(array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function all(array $columns = ['*']): Collection;

    public function get(array $filters = [], array $columns = ['*']): Collection;

    public function first(array $filters = [], array $columns = ['*']): ?Model;

    public function firstOrFail(array $filters = [], array $columns = ['*']): ?Model;

    public function find(mixed $id, ?string $keyName = null, array $columns = ['*']);

    public function exists(array $filters = []): bool;

    public function create(array $data): Model;

    public function update(mixed $id, array $data): Model;

    public function updateOrCreate(mixed $id, array $data): Model;

    public function delete(mixed $id, bool $force = false): bool;

    public function insert(array $data): bool|int;

    public function upsert(array $values, array $uniqueBy, array $update = []): bool|int;

    public function destroy(mixed $ids, bool $force = false): bool|int;

    public function query(array $filters = [], array $columns = ['*']): Builder;

    public function remember(string $cacheKey, mixed $ttl, callable $callback): mixed;
}
