<?php

namespace Modules\Media\Tests\Feature\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Media\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/*
|--------------------------------------------------------------------------
| Test Models for Polymorphic Relations
|--------------------------------------------------------------------------
|
| These dummy models are created specifically for testing polymorphic
| relationships with integer and UUID primary keys. They implement
| the HasMedia interface required by Spatie Media Library.
|
*/

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

/*
|--------------------------------------------------------------------------
| Media Model Feature Tests
|--------------------------------------------------------------------------
*/

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure the database table for TestModelBigInt exists
    if (! Schema::hasTable('test_models_big_int')) {
        Schema::create('test_models_big_int', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
        });
    }

    // Ensure the database table for TestUserUUID exists
    if (! Schema::hasTable('test_users_uuid')) {
        Schema::create('test_users_uuid', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('email')->unique();
        });
    }
});

test('media can be attached to a model with a bigint id and model_id is stored as string', function () {
    $model = TestModelBigInt::create(['name' => 'BigInt Model']);
    $media = $model->addMediaFromString('test-content')
        ->usingFileName('test-bigint.txt')
        ->toMediaCollection();

    // Assert that media was created
    expect($media)->toBeInstanceOf(Media::class);
    expect($media->model_id)->toBeString();
    expect($media->model_id)->toEqual((string) $model->id);
    expect($media->model_type)->toEqual(TestModelBigInt::class);

    // Assert relationship resolves correctly
    $retrievedMedia = Media::find($media->id);
    expect($retrievedMedia->model)->toBeInstanceOf(TestModelBigInt::class);
    expect($retrievedMedia->model->id)->toEqual($model->id);
});

test('media can be attached to a model with a uuid id and model_id is stored as string', function () {
    $user = TestUserUUID::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'UUID User',
        'email' => 'uuid@example.com',
    ]);
    $media = $user->addMediaFromString('test-content')
        ->usingFileName('test-uuid.txt')
        ->toMediaCollection();

    // Assert that media was created
    expect($media)->toBeInstanceOf(Media::class);
    expect($media->model_id)->toBeString();
    expect($media->model_id)->toEqual($user->id);
    expect($media->model_type)->toEqual(TestUserUUID::class);

    // Assert relationship resolves correctly
    $retrievedMedia = Media::find($media->id);
    expect($retrievedMedia->model)->toBeInstanceOf(TestUserUUID::class);
    expect($retrievedMedia->model->id)->toEqual($user->id);
});

test('media model_id attribute is always a string', function () {
    $modelBigInt = TestModelBigInt::create(['name' => 'BigInt Model']);
    $mediaBigInt = $modelBigInt->addMediaFromString('bigint-content')
        ->usingFileName('bigint-string.txt')
        ->toMediaCollection();

    $userUUID = TestUserUUID::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'UUID User',
        'email' => 'uuid-string@example.com',
    ]);
    $mediaUUID = $userUUID->addMediaFromString('uuid-content')
        ->usingFileName('uuid-string.txt')
        ->toMediaCollection();

    // Retrieve media and assert model_id is string type
    expect(Media::find($mediaBigInt->id)->model_id)->toBeString();
    expect(Media::find($mediaUUID->id)->model_id)->toBeString();
});

test('media module column is correctly stored', function () {
    $model = TestModelBigInt::create(['name' => 'Module Test Model']);
    $media = $model->addMediaFromString('module-content')
        ->usingFileName('module.txt')
        ->toMediaCollection();

    // Update the media object's module property directly if not set via custom properties
    $media->module = 'TestModule';
    $media->save();

    expect($media->module)->toEqual('TestModule');
    expect(Media::find($media->id)->module)->toEqual('TestModule');
});
