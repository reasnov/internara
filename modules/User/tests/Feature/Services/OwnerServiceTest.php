<?php

namespace Modules\User\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Permission\Models\Role;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Models\User;
use Modules\User\Services\OwnerService;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure the 'owner' role exists for testing
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Role::updateOrCreate(
        ['id' => (string) Str::uuid(), 'name' => 'owner', 'guard_name' => 'web'],
        ['description' => 'Full system access and ownership', 'module' => 'Core']
    );

    // Ensure the 'admin' role exists for testing other users
    Role::updateOrCreate(
        ['id' => (string) Str::uuid(), 'name' => 'admin', 'guard_name' => 'web'],
        ['description' => 'Admin role for testing', 'module' => 'Core']
    );

    // Bind OwnerService to the container
    $this->ownerService = app(OwnerService::class);
});

test('it can create the first owner account', function () {
    // Assert no owner exists initially
    expect(User::owner()->count())->toBe(0);

    $userData = [
        'name' => 'First Owner',
        'email' => 'owner@example.com',
        'password' => 'password',
    ];

    $owner = $this->ownerService->create($userData);

    // Assert a user is created and has the 'owner' role
    expect($owner)->toBeInstanceOf(User::class);
    expect($owner->hasRole('owner'))->toBeTrue();
    expect($owner->email)->toBe('owner@example.com');

    // Assert only one owner exists in the database
    expect(User::owner()->count())->toBe(1);
    $this->assertDatabaseHas('users', ['id' => $owner->id, 'email' => 'owner@example.com']);
    $this->assertDatabaseHas('model_has_roles', ['model_id' => $owner->id, 'role_id' => Role::where('name', 'owner')->first()->id]);
});

test('it cannot create a second owner account', function () {
    // Create the first owner
    $this->ownerService->create([
        'name' => 'First Owner',
        'email' => 'owner@example.com',
        'password' => 'password',
    ]);

    // Assert owner exists
    expect(User::owner()->count())->toBe(1);

    // Attempt to create a second owner
    $userData = [
        'name' => 'Second Owner',
        'email' => 'second.owner@example.com',
        'password' => 'password',
    ];

    expect(fn () => $this->ownerService->create($userData))
        ->toThrow(AppException::class)
        ->and(fn (AppException $e) => expect($e->getUserMessage())->toBe('shared::exceptions.owner_exists'));

    // Assert only one owner still exists in the database
    expect(User::owner()->count())->toBe(1);
    $this->assertDatabaseMissing('users', ['email' => 'second.owner@example.com']);
});

test('it can retrieve the single owner account', function () {
    // Assert get returns null when no owner exists
    expect($this->ownerService->get())->toBeNull();

    $owner = $this->ownerService->create([
        'name' => 'The Owner',
        'email' => 'the.owner@example.com',
        'password' => 'password',
    ]);

    $retrievedOwner = $this->ownerService->get();

    // Assert the retrieved user is the owner
    expect($retrievedOwner)->toBeInstanceOf(User::class);
    expect($retrievedOwner->id)->toEqual($owner->id);
    expect($retrievedOwner->hasRole('owner'))->toBeTrue();

    // Create another regular user (should not affect get())
    User::factory()->create(['name' => 'Regular User', 'email' => 'regular@example.com'])->assignRole('admin');

    $retrievedOwnerAgain = $this->ownerService->get();
    expect($retrievedOwnerAgain->id)->toEqual($owner->id);
});

test('it returns null when no owner account exists on get', function () {
    expect($this->ownerService->get())->toBeNull();
});

test('it returns true if an owner exists using exists()', function () {
    $this->ownerService->create([
        'name' => 'Test Owner',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    expect($this->ownerService->exists())->toBeTrue();
});

test('it returns false if no owner exists using exists()', function () {
    expect($this->ownerService->exists())->toBeFalse();
});

test('it can update the existing owner account', function () {
    $owner = $this->ownerService->create([
        'name' => 'Old Owner',
        'email' => 'old.owner@example.com',
        'password' => 'password',
    ]);

    $updateData = [
        'name' => 'New Owner Name',
        'email' => 'new.owner@example.com',
    ];

    $updatedOwner = $this->ownerService->update($owner->id, $updateData);

    // Assert owner's details are updated
    expect($updatedOwner)->toBeInstanceOf(User::class);
    expect($updatedOwner->name)->toBe('New Owner Name');
    expect($updatedOwner->email)->toBe('new.owner@example.com');

    // Assert owner still has the 'owner' role
    expect($updatedOwner->hasRole('owner'))->toBeTrue();
    expect(User::owner()->count())->toBe(1); // Still only one owner

    $this->assertDatabaseHas('users', ['id' => $owner->id, 'name' => 'New Owner Name', 'email' => 'new.owner@example.com']);
});

test('it throws RecordNotFoundException if updating a non-existent owner', function () {
    $nonExistentId = (string) Str::uuid(); // Use a UUID as ID type is UUID
    if (config('user.type_id') !== 'uuid') {
        $nonExistentId = 999; // Use an integer if ID type is int
    }

    expect(fn () => $this->ownerService->update($nonExistentId, ['name' => 'Invalid']))
        ->toThrow(RecordNotFoundException::class)
        ->and(fn (RecordNotFoundException $e) => expect($e->getUserMessage())->toBe('shared::exceptions.owner_not_found'));
});

test('it throws RecordNotFoundException if updating with an ID that is not the actual owner', function () {
    $owner = $this->ownerService->create([
        'name' => 'The Owner',
        'email' => 'the.owner@example.com',
        'password' => 'password',
    ]);

    $regularUser = User::factory()->create(); // Create a regular user

    expect(fn () => $this->ownerService->update($regularUser->id, ['name' => 'Attempted Hack']))
        ->toThrow(RecordNotFoundException::class)
        ->and(fn (RecordNotFoundException $e) => expect($e->getUserMessage())->toBe('shared::exceptions.owner_not_found'));
});

test('it does not change owner role during update even if data tries to modify it', function () {
    $owner = $this->ownerService->create([
        'name' => 'The Owner',
        'email' => 'the.owner@example.com',
        'password' => 'password',
    ]);

    // Data attempting to change role
    $updateData = [
        'name' => 'Owner New Name',
        'role' => 'admin', // This should be ignored by OwnerService
        'roles' => ['admin'],
    ];

    $updatedOwner = $this->ownerService->update($owner->id, $updateData);

    expect($updatedOwner->name)->toBe('Owner New Name');
    expect($updatedOwner->hasRole('owner'))->toBeTrue(); // Still owner
    expect($updatedOwner->hasRole('admin'))->toBeFalse(); // Not admin
});

test('it can create a new owner if none exists using updateOrCreate()', function () {
    expect(User::owner()->count())->toBe(0); // No owner initially

    $userData = [
        'name' => 'New Owner',
        'email' => 'new.owner@example.com',
        'password' => 'password',
    ];

    $owner = $this->ownerService->updateOrCreate($userData);

    expect($owner)->toBeInstanceOf(User::class);
    expect($owner->name)->toBe('New Owner');
    expect($owner->hasRole('owner'))->toBeTrue();
    expect(User::owner()->count())->toBe(1);
});

test('it can update an existing owner using updateOrCreate()', function () {
    $existingOwner = $this->ownerService->create([
        'name' => 'Original Owner',
        'email' => 'original.owner@example.com',
        'password' => 'password',
    ]);

    $updateData = [
        'name' => 'Updated Original Owner',
        'email' => 'updated.owner@example.com', // Update email as well
    ];

    $updatedOwner = $this->ownerService->updateOrCreate($updateData);

    expect($updatedOwner)->toBeInstanceOf(User::class);
    expect($updatedOwner->id)->toBe($existingOwner->id); // Same owner
    expect($updatedOwner->name)->toBe('Updated Original Owner');
    expect($updatedOwner->email)->toBe('updated.owner@example.com');
    expect($updatedOwner->hasRole('owner'))->toBeTrue();
    expect(User::owner()->count())->toBe(1); // Still only one owner
});

test('it ensures owner role is assigned by updateOrCreate() even if not in data', function () {
    $userData = [
        'name' => 'Owner with no role in data',
        'email' => 'no.role.data@example.com',
        'password' => 'password',
    ];

    $owner = $this->ownerService->updateOrCreate($userData);
    expect($owner->hasRole('owner'))->toBeTrue();
});

test('it can delete the owner account', function () {
    $owner = $this->ownerService->create([
        'name' => 'The Owner',
        'email' => 'the.owner@example.com',
        'password' => 'password',
    ]);

    expect(User::owner()->count())->toBe(1);

    expect(fn () => $this->ownerService->delete($owner->id))
        ->toThrow(AppException::class)
        ->and(fn (AppException $e) => expect($e->getUserMessage())->toBe('user::exceptions.owner_cannot_be_deleted'));

    // Assert owner still exists (cannot be deleted)
    expect(User::owner()->count())->toBe(1);
    $this->assertDatabaseHas('users', ['id' => $owner->id]);
});

test('it throws RecordNotFoundException if deleting a non-existent owner', function () {
    $nonExistentId = (string) Str::uuid();
    if (config('user.type_id') !== 'uuid') {
        $nonExistentId = 999;
    }

    expect(fn () => $this->ownerService->delete($nonExistentId))
        ->toThrow(RecordNotFoundException::class)
        ->and(fn (RecordNotFoundException $e) => expect($e->getUserMessage())->toBe('The requested record could not be found.'));
});

test('it throws RecordNotFoundException if deleting with an ID that is not the actual owner', function () {
    $owner = $this->ownerService->create([
        'name' => 'The Owner',
        'email' => 'the.owner@example.com',
        'password' => 'password',
    ]);

    $regularUser = User::factory()->create(); // Create a regular user

    expect(fn () => $this->ownerService->delete($regularUser->id))
        ->toThrow(RecordNotFoundException::class)
        ->and(fn (RecordNotFoundException $e) => expect($e->getUserMessage())->toBe('The requested record could not be found.'));
});