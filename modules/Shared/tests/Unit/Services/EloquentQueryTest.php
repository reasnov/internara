<?php

declare(strict_types=1);

namespace Modules\Shared\Tests\Unit\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Shared\Services\EloquentQuery;

/**
 * Concrete Model for testing.
 */
class EloquentQueryTestModel extends Model
{
    protected $table = 'test_eloquent_query_models';

    protected $fillable = ['name', 'value'];

    public $timestamps = false;
}

/**
 * Concrete Service for testing.
 */
class TestEloquentQueryService extends EloquentQuery
{
    public function __construct()
    {
        $this->setModel(new EloquentQueryTestModel);
        $this->setSearchable(['name']);
        $this->setSortable(['name']);
    }
}

beforeEach(function () {
    Schema::create('test_eloquent_query_models', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('value')->default(0);
    });

    $this->service = new TestEloquentQueryService;
});

afterEach(function () {
    Schema::dropIfExists('test_eloquent_query_models');
});

test('it can create a record', function () {
    $record = $this->service->create(['name' => 'Test', 'value' => 100]);

    expect($record)
        ->toBeInstanceOf(EloquentQueryTestModel::class)
        ->and($record->name)
        ->toBe('Test')
        ->and($record->value)
        ->toBe(100);
});

test('it can update a record', function () {
    $record = $this->service->create(['name' => 'Old Name']);

    $updated = $this->service->update($record->id, ['name' => 'New Name']);

    expect($updated->name)->toBe('New Name');
});

test('it throws ModelNotFoundException when updating non-existent record', function () {
    $this->service->update(999, ['name' => 'Fails']);
})->throws(ModelNotFoundException::class);

test('it can paginate records', function () {
    $this->service->create(['name' => 'A']);
    $this->service->create(['name' => 'B']);

    $paginator = $this->service->paginate();

    expect($paginator->total())->toBe(2);
});

test('it can search records', function () {
    $this->service->create(['name' => 'Match']);
    $this->service->create(['name' => 'Other']);

    $results = $this->service->get(['search' => 'Match']);

    expect($results)
        ->toHaveCount(1)
        ->and($results->first()->name)
        ->toBe('Match');
});

test('it can sort records', function () {
    $this->service->create(['name' => 'B']);
    $this->service->create(['name' => 'A']);

    $results = $this->service->get(['sort_by' => 'name', 'sort_dir' => 'asc']);

    expect($results->first()->name)->toBe('A');
});
