<?php

declare(strict_types=1);

namespace Modules\Profile\Services;

use Modules\Profile\Models\Profile;
use Modules\Profile\Services\Contracts\ProfileService as Contract;
use Modules\Shared\Services\EloquentQuery;

/**
 * @property Profile $model
 */
class ProfileService extends EloquentQuery implements Contract
{
    public function __construct(Profile $model)
    {
        $this->setModel($model);
    }

    /**
     * Get or create a profile for a specific user.
     */
    public function getByUserId(string $userId): Profile
    {
        /** @var Profile */
        return $this->model->newQuery()->firstOrCreate(['user_id' => $userId]);
    }
}
