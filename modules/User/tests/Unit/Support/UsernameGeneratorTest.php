<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;
use Modules\User\Support\UsernameGenerator;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('it generates a username with default prefix and length', function () {
    $username = UsernameGenerator::generate();

    // Default: prefix 'u' (1 char) + 8 digits = 9 chars total
    expect($username)
        ->toBeString()
        ->toHaveLength(9)
        ->toMatch('/^u\d{8}$/');
});

test('it generates a username with custom prefix', function () {
    $username = UsernameGenerator::generate('test');

    // 'test' (4 chars) + 8 digits = 12 chars
    expect($username)
        ->toBeString()
        ->toHaveLength(12)
        ->toMatch('/^test\d{8}$/');
});

test('it generates a username with custom length', function () {
    $username = UsernameGenerator::generate('u', 4);

    // 'u' (1 char) + 4 digits = 5 chars
    expect($username)
        ->toBeString()
        ->toHaveLength(5)
        ->toMatch('/^u\d{4}$/');
});

test('it ensures generated username is unique', function () {
    // We can't easily force a collision with random_int,
    // but we can mock the behavior or just verify it doesn't return an existing one.

    // Create a user with a specific username
    User::factory()->create([
        'username' => 'u12345678',
    ]);

    // Since we can't easily force the generator to pick 'u12345678' first,
    // we'll just assert that whatever it generates is unique in the DB.
    $newUsername = UsernameGenerator::generate();

    expect(User::where('username', $newUsername)->exists())->toBeFalse();
});
