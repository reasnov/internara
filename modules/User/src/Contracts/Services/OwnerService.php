<?php

namespace Modules\User\Contracts\Services;

interface OwnerService
{
    /**
     * Check if a record exists based on the given conditions.
     *
     * @param  array<string, mixed>|callable  $where  Conditions for existence check.
     * @return bool True if a record exists, false otherwise.
     */
    public function exists(array|callable $where = []): bool;
}
