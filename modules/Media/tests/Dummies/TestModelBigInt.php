<?php

namespace Modules\Media\Tests\Dummies;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// Dummy model with auto-incrementing (bigint) ID
class TestModelBigInt extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'test_models_big_int';

    protected $guarded = [];

    public $timestamps = false; // Not needed for these tests

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default');
    }
}
