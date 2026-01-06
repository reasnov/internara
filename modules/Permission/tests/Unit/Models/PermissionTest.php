<?php

namespace Modules\Permission\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('it can create a permission using factory', function () {
    $permission = Permission::factory()->create([
        'name' => 'edit_posts',
        'guard_name' => 'web',
        'module' => 'Post',
    ]);

    expect($permission)
        ->toBeInstanceOf(Permission::class)
        ->name->toBe('edit_posts')
        ->guard_name->toBe('web')
        ->module->toBe('Post');

    if (config('permission.model_key_type') === 'uuid') {
        expect($permission->id)->toBeString()->and(Str::isUuid($permission->id))->toBeTrue();
    } else {
        expect($permission->id)->toBeInt();
    }
});

test('it enforces unique permission name per guard', function () {
    Permission::factory()->create(['name' => 'delete_users', 'guard_name' => 'web']);

    // Attempting to create duplicate should fail
    expect(fn () => Permission::factory()->create(['name' => 'delete_users', 'guard_name' => 'web']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('it allows same permission name on different guards', function () {
    Permission::factory()->create(['name' => 'delete_users', 'guard_name' => 'web']);
    $apiPerm = Permission::factory()->create(['name' => 'delete_users', 'guard_name' => 'api']);

    expect($apiPerm)->exists->toBeTrue();
});
