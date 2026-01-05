<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;
use Modules\User\Models\User;

// Use UserTestCase for feature testing in the User module
uses(\Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // We'll resolve the AuthService here to ensure we're testing the bound implementation
    $this->authService = $this->app->make(AuthService::class);
});

test('it can create a user directly', function () {
    $user = User::create([
        'name' => 'Direct User',
        'email' => 'direct@example.com',
        'password' => Hash::make('password'),
    ]);
    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', ['email' => 'direct@example.com']);
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

    try {
        $this->authService->login([
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);
        $this->fail('AppException was not thrown');
    } catch (AppException $e) {
        expect($e->getCode())->toBe(401);
        expect($e->getMessage())->toBe('Authentication attempt failed for: user@example.com');
        expect($e->getUserMessage())->toBe(__('user::exceptions.invalid_credentials'));
    }
});

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

test('a user can change their password with correct current password', function () {
    $user = User::factory()->create(['password' => Hash::make('current-password')]);

    $result = $this->authService->changePassword($user, 'current-password', 'new-password');

    expect($result)->toBeTrue();
    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

test('a user cannot change their password with incorrect current password', function () {
    $user = User::factory()->create(['password' => Hash::make('current-password')]);

    try {
        $this->authService->changePassword($user, 'wrong-password', 'new-password');
        $this->fail('AppException was not thrown');
    } catch (AppException $e) {
        expect($e->getCode())->toBe(422);
        expect($e->getMessage())->toBe('user::exceptions.password_mismatch');
        expect($e->getUserMessage())->toBe(__('user::exceptions.password_mismatch'));
    }
});

test('it sends a password reset link', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->authService->sendPasswordResetLink($user->email);

    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
});

test('it resets password with a valid token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $credentials = [
        'email' => $user->email,
        'password' => 'new-awesome-password',
        'password_confirmation' => 'new-awesome-password',
        'token' => $token,
    ];

    $result = $this->authService->resetPassword($credentials);

    expect($result)->toBeTrue();
    $user->refresh();
    expect(Hash::check('new-awesome-password', $user->password))->toBeTrue();
});

test('it sends an email verification notification on registration', function () {
    Notification::fake();

    $userData = [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password',
    ];

    $user = $this->authService->register($userData);

    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
});

test('it can resend an email verification notification', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->authService->resendVerificationEmail($user);

    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
});

test('it does not resend verification if email is already verified', function () {
    Notification::fake();
    $user = User::factory()->create(); // email_verified_at is set by default

    try {
        $this->authService->resendVerificationEmail($user);
        $this->fail('AppException was not thrown');
    } catch (AppException $e) {
        expect($e->getCode())->toBe(422);
        expect($e->getMessage())->toBe('user::exceptions.email_already_verified');
        expect($e->getUserMessage())->toBe(__('user::exceptions.email_already_verified'));
    }
});

test('it can verify an email with a valid hash', function () {
    Event::fake();
    $user = User::factory()->unverified()->create();
    $hash = sha1($user->getEmailForVerification());

    $result = $this->authService->verifyEmail($user->getKey(), $hash);

    expect($result)->toBeTrue();
    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class);
});

test('it confirms password successfully with correct password', function () {
    $user = User::factory()->create(['password' => Hash::make('password123')]);

    $result = $this->authService->confirmPassword($user, 'password123');

    expect($result)->toBeTrue();
});

test('it fails password confirmation with incorrect password', function () {
    $user = User::factory()->create(['password' => Hash::make('password123')]);

    $result = $this->authService->confirmPassword($user, 'wrong-password');

    expect($result)->toBeFalse();
});
