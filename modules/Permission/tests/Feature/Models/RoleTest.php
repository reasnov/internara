<?php

namespace Modules\Permission\Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Permission\Models\Role;

uses(RefreshDatabase::class);

test('it can create a role using factory', function () {
    $role = Role::factory()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'module' => 'User',
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

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'admin',
        'guard_name' => 'web',
        'module' => 'User',
    ]);
});

test('it enforces unique name per guard', function () {
    Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']);

    // Attempting to create duplicate should fail
    expect(fn () => Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']))
        ->toThrow(\Illuminate\Database\QueryException::class);

    $this->assertDatabaseCount('roles', 1);
});

test('it allows same name on different guards', function () {
    Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']);
    $apiRole = Role::factory()->create(['name' => 'editor', 'guard_name' => 'api']);

    expect($apiRole)->exists->toBeTrue();
    $this->assertDatabaseCount('roles', 2);
    $this->assertDatabaseHas('roles', ['name' => 'editor', 'guard_name' => 'api']);
});
