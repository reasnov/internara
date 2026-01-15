<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Modules\User\Services\Contracts\UserService;
use Modules\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'student', 'guard_name' => 'web']);
});

test('UserService automatically verifies email for admin role', function () {
    $userService = app(UserService::class);

    $user = $userService->create([
        'name'     => 'Admin User',
        'email'    => 'admin@example.com',
        'password' => 'password123',
        'roles'    => ['admin'],
    ]);

    expect($user->hasVerifiedEmail())->toBeTrue();
    expect($user->hasRole('admin'))->toBeTrue();
});

test('UserService assigns student role by default if not specified', function () {
    $userService = app(UserService::class);

    $user = $userService->create([
        'name'     => 'Regular User',
        'email'    => 'user@example.com',
        'password' => 'password123',
    ]);

    expect($user->hasRole('student'))->toBeTrue();
    expect($user->hasVerifiedEmail())->toBeFalse();
});
