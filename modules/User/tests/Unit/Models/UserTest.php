<?php

namespace Modules\User\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

test('it uses uuid as primary key', function () {
    $user = User::factory()->create();

    expect($user->id)->toBeString()
        ->and(Str::isUuid($user->id))->toBeTrue();
});

test('it has fillable attributes', function () {
    $fillable = [
        'name',
        'email',
        'username',
        'password',
        'avatar_url',
    ];

    $user = new User;

    expect($user->getFillable())->toBe($fillable);
});

test('it hides sensitive attributes', function () {
    $hidden = [
        'password',
        'remember_token',
    ];

    $user = new User;

    expect($user->getHidden())->toBe($hidden);
});

test('it casts attributes correctly', function () {
    $user = User::factory()->create([
        'email_verified_at' => '2023-01-01 12:00:00',
        'password' => 'secret',
    ]);

    expect($user->email_verified_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and(Hash::check('secret', $user->password))->toBeTrue();
});

test('it generates correct initials', function (string $name, string $initials) {
    $user = User::factory()->make(['name' => $name]);

    expect($user->initials())->toBe($initials);
})->with([
    ['John Doe', 'JD'],
    ['John', 'J'],
    ['John Doe Smith', 'JD'],
    ['Jane Mary Doe', 'JM'],
]);

test('it automatically generates a username if not provided', function () {
    $user = User::factory()->create(['username' => null]);

    expect($user->username)
        ->not->toBeNull()
        ->toBeString()
        ->toMatch('/^u\d{8}$/');
});
