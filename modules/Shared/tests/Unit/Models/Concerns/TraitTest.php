<?php

declare(strict_types=1);

namespace Modules\Shared\Tests\Unit\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Shared\Models\Concerns\HasStatus;
use Modules\Shared\Models\Concerns\HasUuid;

uses(RefreshDatabase::class);

class TraitTestModel extends Model
{
    use HasStatus, HasUuid;

    protected $table = 'trait_test_models';

    protected $fillable = ['name'];
}

beforeEach(function () {
    Schema::create('trait_test_models', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('trait_test_models');
});

test('HasUuid trait generates uuid on creation', function () {
    $model = TraitTestModel::create(['name' => 'Test']);

    expect($model->id)
        ->not->toBeNull()
        ->and(strlen($model->id))
        ->toBe(36);
});

test('HasStatus trait can set and get status', function () {
    $model = TraitTestModel::create(['name' => 'Test']);

    $model->setStatus('active');

    $model = $model->fresh();

    // Debug: check if statuses table has data

    // \Illuminate\Support\Facades\Log::info('Statuses count: ' . \Illuminate\Support\Facades\DB::table('statuses')->count());

    // \Illuminate\Support\Facades\Log::info('First status: ' . json_encode(\Illuminate\Support\Facades\DB::table('statuses')->first()));

    expect($model->statuses)
        ->toHaveCount(1)

        ->and($model->latestStatus()->name)
        ->toBe('active')

        ->and($model->getStatusColor())
        ->toBe('success');
});
