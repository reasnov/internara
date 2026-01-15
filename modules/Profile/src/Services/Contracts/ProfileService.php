<?php

declare(strict_types=1);

namespace Modules\Profile\Services\Contracts;

use Modules\Profile\Models\Profile;
use Modules\Shared\Services\Contracts\EloquentQuery;

/**
 * @extends EloquentQuery<Profile>
 */
interface ProfileService extends EloquentQuery
{
    /**
     * Get or create a profile for a specific user.
     */
    public function getByUserId(string $userId): Profile;
}
