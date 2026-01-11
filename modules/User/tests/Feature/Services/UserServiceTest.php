<?php

namespace Modules\User\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery;
use Modules\Permission\Models\Role;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Contracts\Services\OwnerService as OwnerServiceContract;
use Modules\User\Models\User;
use Modules\User\Services\UserService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->ownerService = Mockery::mock(OwnerServiceContract::class);
    $this->service = new UserService(new User, $this->ownerService);

    // Create 'owner' role for tests
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Role::firstOrCreate(
        ['name' => 'owner', 'guard_name' => 'web'],
        ['id' => (string) Str::uuid()]
    );
});

test('it can create a user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $this->ownerService->shouldNotReceive('exists');
    $this->ownerService->shouldNotReceive('create'); // Should not call ownerService->create

    $user = $this->service->create($data);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Test User')
        ->email->toBe('test@example.com');

    // Password should be hashed (handled by Model casts)
    expect(Hash::check('password123', $user->password))->toBeTrue();

    // Username should be auto-generated (handled by Model booted)
    expect($user->username)->not->toBeNull();
});

test('it throws AppException for duplicate email on create', function () {
    User::factory()->create(['email' => 'duplicate@example.com']);

    $data = [
        'name' => 'New User',
        'email' => 'duplicate@example.com',
        'password' => 'password123',
    ];

    expect(fn () => $this->service->create($data))
        ->toThrow(AppException::class)
        ->and(fn (AppException $e) => expect($e->getUserMessage())->toBe('shared::exceptions.name_exists'));
});

test('it can create an owner user', function () {
    $data = [
        'name' => 'Owner User',
        'email' => 'owner@example.com',
        'password' => 'password123',
        'roles' => 'owner',
    ];

    $mockOwnerUser = Mockery::mock(User::class);
    // When OwnerService::create is called, it directly returns the user.
    // The role assignment is done inside OwnerService.
    // So, we expect assignRole to be called on the *returned* user if it were a full model,
    // but here we are mocking the return value of ownerService->create
    // The assignRole is now internal to OwnerService::create and not called on this mock.

    $this->ownerService->shouldReceive('exists')->andReturn(false)->once();
    $this->ownerService->shouldReceive('create')->once()->with(
        Mockery::on(function ($arg) {
            return $arg['name'] === 'Owner User';
        })
    )->andReturn($mockOwnerUser);

    $user = $this->service->create($data);

    expect($user)->toBe($mockOwnerUser);
});

test('it can list users without filters', function () {
    User::factory()->count(5)->create();

    $results = $this->service->list();

    expect($results)->toHaveCount(5);
    expect($results->currentPage())->toBe(1);
    expect($results->perPage())->toBe(10);
});

test('it can list users with pagination', function () {
    User::factory()->count(15)->create();

    $results = $this->service->list([], 5); // 5 items per page

    expect($results)->toHaveCount(5);
    expect($results->total())->toBe(15);
    expect($results->perPage())->toBe(5);
    expect($results->lastPage())->toBe(3);
});

test('it can list users with search', function () {
    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Doe']);
    User::factory()->create(['name' => 'Bob Smith']);

    $results = $this->service->list(['search' => 'Doe']);

    expect($results)->toHaveCount(2);
    expect($results->items()[0]->name)->toContain('Doe');
    expect($results->items()[1]->name)->toContain('Doe');
});

test('it can list users with sorting', function () {
    User::factory()->create(['name' => 'Alice']);
    User::factory()->create(['name' => 'Bob']);
    User::factory()->create(['name' => 'Charlie']);

    $resultsAsc = $this->service->list(['sort' => 'name', 'direction' => 'asc']);
    expect($resultsAsc->first()->name)->toBe('Alice');
    expect($resultsAsc->last()->name)->toBe('Charlie');

    $resultsDesc = $this->service->list(['sort' => 'name', 'direction' => 'desc']);
    expect($resultsDesc->first()->name)->toBe('Charlie');
    expect($resultsDesc->last()->name)->toBe('Alice');
});

test('it can update a user', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $this->ownerService->shouldNotReceive('update'); // Should not call ownerService->update
    $this->ownerService->shouldReceive('exists')->andReturn(false); // If it gets called for non-owner case

    $updatedUser = $this->service->update($user->id, [
        'name' => 'New Name',
    ]);

    expect($updatedUser->name)->toBe('New Name');
    expect(User::find($user->id)->name)->toBe('New Name');
});

test('it throws RecordNotFoundException when updating a non-existent user', function () {
    $nonExistentId = (string) Str::uuid();

    expect(fn () => $this->service->update($nonExistentId, ['name' => 'Any Name']))
        ->toThrow(RecordNotFoundException::class)
        ->and(fn (RecordNotFoundException $e) => expect($e->replace['id'])->toBe($nonExistentId));
});

test('it can update an owner user', function () {
    // Create an actual owner user in the database
    $ownerUser = User::factory()->create();
    $ownerUser->assignRole('owner'); // Assign owner role

    $data = [
        'name' => 'Updated Owner Name',
    ];

    $mockUpdatedOwner = Mockery::mock(User::class);
    // When OwnerService::update is called, it directly returns the user.

    $this->ownerService->shouldReceive('update')->once()->with(
        $ownerUser->id,
        Mockery::on(function ($arg) {
            return $arg['name'] === 'Updated Owner Name';
        }),
        Mockery::any()
    )->andReturn($mockUpdatedOwner);

    $updatedUser = $this->service->update($ownerUser->id, $data);

    expect($updatedUser)->toBe($mockUpdatedOwner);
});

test('it does not overwrite password if empty on update', function () {
    $user = User::factory()->create(['password' => 'original-password']);
    $oldHash = $user->password;

    $this->ownerService->shouldNotReceive('update'); // Should not call ownerService->update
    $this->ownerService->shouldReceive('exists')->andReturn(false); // If it gets called for non-owner case

    $this->service->update($user->id, [
        'name' => 'Updated Name',
        'password' => '', // Should be ignored
    ]);

    $user->refresh();

    expect($user->password)->toBe($oldHash);
});

test('it can delete a user', function () {
    $user = User::factory()->create();

    $this->ownerService->shouldNotReceive('delete'); // Should not call ownerService->delete

    $result = $this->service->delete($user->id);

    expect($result)->toBeTrue();
    expect(User::find($user->id))->toBeNull();
});

test('it throws RecordNotFoundException when deleting a non-existent user', function () {
    $nonExistentId = (string) Str::uuid();

    expect(fn () => $this->service->delete($nonExistentId))
        ->toThrow(RecordNotFoundException::class)
        ->and(fn (RecordNotFoundException $e) => expect($e->replace['id'])->toBe($nonExistentId));
});

test('it cannot delete an owner user', function () {
    // Create an actual owner user in the database
    $ownerUser = User::factory()->create();
    $ownerUser->assignRole('owner'); // Assign owner role

    $this->ownerService->shouldReceive('delete')->once()->with($ownerUser->id)
        ->andThrow(new AppException('user::exceptions.owner_cannot_be_deleted', replace: [], code: 403));

    expect(fn () => $this->service->delete($ownerUser->id))
        ->toThrow(AppException::class)
        ->and(fn (AppException $e) => expect($e->getUserMessage())->toBe('user::exceptions.owner_cannot_be_deleted'));
});

test('it can find by unique fields', function () {
    $user = User::factory()->create([
        'email' => 'unique@example.com',
        'username' => 'uniqueuser',
    ]);

    expect($this->service->find($user->id))->toBeInstanceOf(User::class)
        ->and($this->service->find($user->id)->id)->toBe($user->id);
    expect($this->service->findByEmail('unique@example.com'))->toBeInstanceOf(User::class)
        ->and($this->service->findByEmail('unique@example.com')->id)->toBe($user->id);
    expect($this->service->findByUsername('uniqueuser'))->toBeInstanceOf(User::class)
        ->and($this->service->findByUsername('uniqueuser')->id)->toBe($user->id);
});

test('it returns null when finding a non-existent user by ID', function () {
    $nonExistentId = (string) Str::uuid();
    expect($this->service->find($nonExistentId))->toBeNull();
});

test('it returns null when finding a non-existent user by email', function () {
    expect($this->service->findByEmail('nonexistent@example.com'))->toBeNull();
});

test('it returns null when finding a non-existent user by username', function () {
    expect($this->service->findByUsername('nonexistentuser'))->toBeNull();
});

test('it returns an eloquent builder instance for query()', function () {
    $builder = $this->service->query();
    expect($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

test('it returns an eloquent builder instance with selected columns for query()', function () {
    User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);
    $builder = $this->service->query(['name']);
    $user = $builder->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Test User');
    expect($user)->not->toHaveProperty('email'); // Should not have email if not selected
});

test('it returns true if a record exists with exists()', function () {
    User::factory()->create(['email' => 'existing@example.com']);
    expect($this->service->exists(['email' => 'existing@example.com']))->toBeTrue();
});

test('it returns false if no record exists with exists()', function () {
    expect($this->service->exists(['email' => 'nonexistent@example.com']))->toBeFalse();
});

test('it returns true if any record exists when calling exists() without conditions', function () {
    User::factory()->create();
    expect($this->service->exists())->toBeTrue();
});

test('it returns false if no record exists when calling exists() without conditions', function () {
    expect($this->service->exists())->toBeFalse();
});