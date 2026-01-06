<?php

namespace Modules\User\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Modules\User\Services\UserService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new UserService(new User);
});

test('it can create a user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

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

    $updatedUser = $this->service->update($user->id, [
        'name' => 'New Name',
    ]);

    expect($updatedUser->name)->toBe('New Name');
    expect(User::find($user->id)->name)->toBe('New Name');
});

test('it does not overwrite password if empty on update', function () {
    $user = User::factory()->create(['password' => 'original-password']);
    $oldHash = $user->password;

    $this->service->update($user->id, [
        'name' => 'Updated Name',
        'password' => '', // Should be ignored
    ]);

    $user->refresh();

    expect($user->password)->toBe($oldHash);
});

test('it can delete a user', function () {
    $user = User::factory()->create();

    $result = $this->service->delete($user->id);

    expect($result)->toBeTrue();
    expect(User::find($user->id))->toBeNull();
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
