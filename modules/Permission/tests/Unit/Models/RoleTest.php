<?php

use Modules\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('it can create a role using factory', function () {
    $role = Role::factory()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'module' => 'User'
    ]);

    expect($role)
        ->toBeInstanceOf(Role::class)
        ->name->toBe('admin')
        ->guard_name->toBe('web')
        ->module->toBe('User');

    if (config('permission.model_key_type') === 'uuid') {
        expect($role->id)->toBeString()->and(Str::isUuid($role->id))->toBeTrue();
    } else {
        expect($role->id)->toBeInt();
    }
});

test('it enforces unique name per guard', function () {
    Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']);

    // Attempting to create duplicate should fail
    expect(fn () => Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('it allows same name on different guards', function () {
    Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']);
    $apiRole = Role::factory()->create(['name' => 'editor', 'guard_name' => 'api']);

    expect($apiRole)->exists->toBeTrue();
});
