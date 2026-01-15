<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Livewire\Livewire;
use Modules\Auth\Livewire\Register;
use Modules\User\Models\User;
use Modules\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure essential roles exist
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::create(['name' => 'student', 'guard_name' => 'web']);
});

test('a new user can register and receives a student role and verification email', function () {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('name', 'Test Student')
        ->set('email', 'student@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors();

    $user = User::where('email', 'student@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->hasRole('student'))->toBeTrue();
    expect($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});
