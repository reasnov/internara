<?php

namespace Modules\User\Tests\Feature\Livewire\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;
use Modules\User\Livewire\Auth\Login;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    // We will mock the AuthService to control its behavior during tests
    $this->authServiceMock = $this->mock(AuthService::class);
    $this->app->instance(AuthService::class, $this->authServiceMock);

    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
});

test('a user can log in with correct credentials and be redirected to dashboard', function () {
    $this->authServiceMock->shouldReceive('login')
        ->once()
        ->with([
            'identifier' => 'test@example.com',
            'password' => 'password',
        ], false)
        ->andReturn($this->user);

    Livewire::test(Login::class)
        ->set('identifier', 'test@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));
});

test('login fails with incorrect credentials and displays an error', function () {
    $errorMessage = 'Invalid credentials provided.';
    $this->authServiceMock->shouldReceive('login')
        ->once()
        ->andThrow(new AppException(userMessage: $errorMessage, code: 401));

    Livewire::test(Login::class)
        ->set('identifier', 'test@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['identifier' => $errorMessage])
        ->assertNoRedirect();

    $this->assertGuest();
});

test('login with remember me option works correctly', function () {
    $this->authServiceMock->shouldReceive('login')
        ->once()
        ->with([
            'identifier' => 'test@example.com',
            'password' => 'password',
        ], true)
        ->andReturn($this->user);

    Livewire::test(Login::class)
        ->set('identifier', 'test@example.com')
        ->set('password', 'password')
        ->set('remember', true)
        ->call('login')
        ->assertRedirect(route('dashboard'));
});

test('validation errors are displayed for empty credentials', function () {
    $this->authServiceMock->shouldNotReceive('login');

    Livewire::test(Login::class)
        ->set('identifier', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['identifier' => 'required', 'password' => 'required']);

    $this->assertGuest();

});

test('validation passes for invalid email format since only string rule is present', function () {
    $errorMessage = 'Invalid credentials provided.';
    $this->authServiceMock->shouldReceive('login')
        ->once()
        ->with([
            'identifier' => 'invalid-email-format',
            'password' => 'password',
        ], false)
        ->andThrow(new AppException(userMessage: $errorMessage, code: 401));

    Livewire::test(Login::class)
        ->set('identifier', 'invalid-email-format')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['identifier' => $errorMessage])
        ->assertNoRedirect();

    $this->assertGuest();
});

test('validation errors are displayed for empty email', function () {
    Livewire::test(Login::class)

        ->set('identifier', '')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['identifier' => 'required']);
});
