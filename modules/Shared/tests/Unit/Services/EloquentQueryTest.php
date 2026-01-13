<?php

namespace Modules\Shared\Tests\Unit\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Modules\Shared\Services\EloquentQuery;
use Tests\TestCase;

// Define a concrete Model for testing purposes.
class EloquentQueryTestModel extends Model
{
    protected $table = 'eloquent_query_test_models';

    protected $guarded = [];
    protected $fillable = ['name', 'description', 'value'];

    public $timestamps = false;
}

// Define a concrete EloquentQuery implementation for the test model.
class TestEloquentQuery extends EloquentQuery
{
    public function __construct()
    {
        $this->setModel(new EloquentQueryTestModel());
        $this->setSearchable(['name', 'description']);
        $this->setSortable(['name', 'value']);
    }
}



beforeEach(function () {
    // Set up the database schema for the test model.
    Schema::create('eloquent_query_test_models', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('description')->nullable();
        $table->integer('value')->default(0);
    });

    // Seed the database with some test data.
    EloquentQueryTestModel::insert([
        ['name' => 'Alpha', 'description' => 'First item', 'value' => 10],
        ['name' => 'Bravo', 'description' => 'Second item', 'value' => 20],
        ['name' => 'Charlie', 'description' => 'Third item, special', 'value' => 30],
        ['name' => 'Delta', 'description' => 'Fourth item', 'value' => 10],
    ]);

    $this->query = new TestEloquentQuery();
});

test('it can be instantiated', function () {
    expect($this->query)->toBeInstanceOf(TestEloquentQuery::class);
});

test('it retrieves all records', function () {
    $all = $this->query->all();
    expect($all)->toHaveCount(4);
    expect($all->first()->name)->toBe('Alpha');
});

test('it retrieves records with filters', function () {
    $results = $this->query->get(['value' => 10]);
    expect($results)->toHaveCount(2);
    expect($results->pluck('name')->all())->toBe(['Alpha', 'Delta']);
});

test('it retrieves paginated records', function () {
    $paginator = $this->query->paginate([], 2);
    expect($paginator)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($paginator->total())->toBe(4);
    expect($paginator->count())->toBe(2);
});

test('it retrieves the first record matching filters', function () {
    $model = $this->query->first(['value' => 20]);
    expect($model)->not->toBeNull();
    expect($model->name)->toBe('Bravo');
});

test('it retrieves the first record or fails', function () {
    $model = $this->query->firstOrFail(['name' => 'Charlie']);
    expect($model->name)->toBe('Charlie');
});

test('it throws model not found exception', function () {
    $this->query->firstOrFail(['name' => 'Foxtrot']);
})->throws(ModelNotFoundException::class);

test('it finds a record by primary key', function () {
    $model = $this->query->find(1);
    expect($model->id)->toBe(1);
    expect($model->name)->toBe('Alpha');
});

test('it checks if a record exists', function () {
    expect($this->query->exists(['name' => 'Delta']))->toBeTrue();
    expect($this->query->exists(['name' => 'Echo']))->toBeFalse();
});

test('it creates a new record', function () {
    $model = $this->query->create(['name' => 'Echo', 'value' => 50, 'description' => 'Fifth item']);
    expect($model)->toBeInstanceOf(EloquentQueryTestModel::class);
    $this->assertDatabaseHas('eloquent_query_test_models', ['name' => 'Echo']);
});

test('it updates a record', function () {
    $model = $this->query->update(1, ['name' => 'Alpha Updated']);
    expect($model->name)->toBe('Alpha Updated');
    $this->assertDatabaseHas('eloquent_query_test_models', ['id' => 1, 'name' => 'Alpha Updated']);
});

test('it updates or creates a record', function () {
    // Test update
    $this->query->save(['name' => 'Alpha'], ['value' => 15]);
    $this->assertDatabaseHas('eloquent_query_test_models', ['name' => 'Alpha', 'value' => 15]);

    // Test create
    $this->query->save(['name' => 'Foxtrot'], ['value' => 60]);
    $this->assertDatabaseHas('eloquent_query_test_models', ['name' => 'Foxtrot', 'value' => 60]);
});

test('it deletes a record', function () {
    $result = $this->query->delete(1);
    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('eloquent_query_test_models', ['id' => 1]);
});

test('it handles deleting non-existent record', function () {
    $result = $this->query->delete(99);
    expect($result)->toBeFalse();
});

test('it performs bulk insert', function () {
    $data = [
        ['name' => 'Golf', 'value' => 70],
        ['name' => 'Hotel', 'value' => 80],
    ];
    $result = $this->query->insert($data);
    expect($result)->toBeTrue();
    $this->assertDatabaseHas('eloquent_query_test_models', ['name' => 'Golf']);
    $this->assertDatabaseHas('eloquent_query_test_models', ['name' => 'Hotel']);
});

test('it performs bulk upsert', function () {
    $values = [
        ['id' => 1, 'name' => 'Alpha Updated', 'value' => 11], // Update
        ['id' => 5, 'name' => 'India', 'value' => 90],      // Insert
    ];
    $this->query->upsert($values, 'id', ['name', 'value']);
    $this->assertDatabaseHas('eloquent_query_test_models', ['id' => 1, 'name' => 'Alpha Updated']);
    $this->assertDatabaseHas('eloquent_query_test_models', ['id' => 5, 'name' => 'India']);
});

test('it destroys multiple records', function () {
    $count = $this->query->destroy([1, 2]);
    expect($count)->toBe(2);
    $this->assertDatabaseMissing('eloquent_query_test_models', ['id' => 1]);
    $this->assertDatabaseMissing('eloquent_query_test_models', ['id' => 2]);
});

test('it converts results to an array', function () {
    $array = $this->query->toArray(['value' => 10]);
    expect($array)->toBeArray();
    expect($array)->toHaveCount(2);
    expect($array[0]['name'])->toBe('Alpha');
});

test('it applies search filters', function () {
    expect($results)->toHaveCount(4);

    $results = $this->query->get(['search' => 'special']);
    expect($results)->toHaveCount(1);
    expect($results->first()->name)->toBe('Charlie');
});

test('it applies sorting', function () {
    $results = $this->query->get(['sort_by' => 'name', 'sort_dir' => 'desc']);
    expect($results->pluck('name')->all())->toBe(['Delta', 'Charlie', 'Bravo', 'Alpha']);

    $results = $this->query->get(['sort_by' => 'value', 'sort_dir' => 'asc']);
    expect($results->pluck('value')->all())->toBe([10, 10, 20, 30]);
});

test('it combines search and sort', function () {
    $filters = [
        'search' => 'item',
        'sort_by' => 'name',
        'sort_dir' => 'asc',
    ];
    $results = $this->query->get($filters);
    expect($results)->toHaveCount(4);
    expect($results->pluck('name')->all())->toBe(['Alpha', 'Bravo', 'Delta']);
});

test('it uses remember to cache results', function () {
    $cacheKey = 'eloquent-query-test';

    // 1. Run the query and cache the result
    $firstResult = $this->query->remember($cacheKey, 60, function ($query) {
        return $query->get(['value' => 10]);
    });

    expect(Cache::get($cacheKey))->toEqual($firstResult);

    // 2. Modify database - cache should still return old result
    EloquentQueryTestModel::where('value', 10)->delete();
    $cachedResult = $this->query->remember($cacheKey, 60, function ($query) {
        return $query->get(['value' => 10]);
    });
    expect($cachedResult)->toEqual($firstResult);
    expect($cachedResult)->toHaveCount(2);

    // 3. Run with skipCache - should return fresh (empty) result
    $freshResult = $this->query->remember($cacheKey, 60, function ($query) {
        return $query->get(['value' => 10]);
    }, true);
    expect($freshResult)->toHaveCount(0);
    // ensure cache was forgotten
    expect(Cache::get($cacheKey))->toBeNull();
});

test('it can build upon a base query', function () {
    $baseQuery = EloquentQueryTestModel::where('value', '>', 15);
    $this->query->setBaseQuery($baseQuery);

    $results = $this->query->get();
    expect($results)->toHaveCount(2);
    expect($results->pluck('name')->all())->toBe(['Bravo', 'Charlie']);
});
