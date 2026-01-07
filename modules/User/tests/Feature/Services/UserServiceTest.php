<?php

namespace Modules\User\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery;
use Modules\Permission\Models\Role;
use Modules\Shared\Exceptions\AppException;
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

test('it can list users with search', function () {
    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Doe']);
    User::factory()->create(['name' => 'Bob Smith']);

    $results = $this->service->list(['search' => 'Doe']);

    expect($results)->toHaveCount(2);
    expect($results->items()[0]->name)->toContain('Doe');
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

    expect($this->service->find($user->id)->id)->toBe($user->id);
    expect($this->service->findByEmail('unique@example.com')->id)->toBe($user->id);
    expect($this->service->findByUsername('uniqueuser')->id)->toBe($user->id);
});
