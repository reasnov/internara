<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;
use Modules\User\Models\User;
use Tests\TestCase;

// Use TestCase and RefreshDatabase for feature testing
uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // We'll resolve the AuthService here to ensure we're testing the bound implementation
    $this->authService = $this->app->make(AuthService::class);
});

test('a user can register successfully', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ];

    $user = $this->authService->register($userData);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect(Hash::check('password', $user->password))->toBeTrue();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

test('registration fails with duplicate email', function () {
    User::factory()->create(['email' => 'duplicate@example.com']);

    $userData = [
        'name' => 'Another User',
        'email' => 'duplicate@example.com',
        'password' => 'password',
    ];

    $this->authService->register($userData);
})->throws(AppException::class, 'Attempted to register with duplicate email: duplicate@example.com');

test('a user can log in with correct credentials', function () {
    $password = 'secret123';
    $user = User::factory()->create(['password' => Hash::make($password)]);

    $loggedInUser = $this->authService->login([
        'email' => $user->email,
        'password' => $password,
    ]);

    expect($loggedInUser->id)->toBe($user->id);
    $this->assertAuthenticatedAs($user);
});

test('login fails with incorrect credentials', function () {
    User::factory()->create(['email' => 'user@example.com', 'password' => Hash::make('password')]);

    $this->authService->login([
        'email' => 'user@example.com',
        'password' => 'wrong-password',
    ]);
})->throws(AppException::class, 'Authentication attempt failed for email: user@example.com');

test('a user can be logged out', function () {
    $user = User::factory()->create();
    $this->actingAs($user); // Authenticate the user

    $this->authService->logout();

    $this->assertGuest(); // Assert that no user is authenticated
});

test('get authenticated user returns correct user when logged in', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $authenticatedUser = $this->authService->getAuthenticatedUser();

    expect($authenticatedUser->id)->toBe($user->id);
});

test('get authenticated user returns null when not logged in', function () {
    $authenticatedUser = $this->authService->getAuthenticatedUser();

    expect($authenticatedUser)->toBeNull();
});
