<?php

namespace Modules\Media\Tests\Dummies;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// Dummy model with UUID ID
class TestUserUUID extends Model implements HasMedia
{
    use HasUuids;
    use InteractsWithMedia;

    protected $table = 'test_users_uuid';

    protected $guarded = [];

    public $timestamps = false; // Not needed for these tests

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default');
    }
}
