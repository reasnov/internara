<?php

declare(strict_types=1);

namespace Modules\User\Services\Contracts;

use Modules\Shared\Services\Contracts\EloquentQuery;
use Modules\User\Models\User;

interface SuperAdminService extends EloquentQuery
{
    /**
     * Get the single SuperAdmin user.
     *
     * @param array<int, string> $columns Columns to retrieve.
     *
     * @return \Modules\User\Models\User|null The SuperAdmin user or null if not found.
     */
    public function getSuperAdmin(array $columns = ['*']): ?User;
}
