<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Permission\Models\Permission;
use Modules\Permission\Services\PermissionService;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new PermissionService;
});

test('it can list and filter permissions', function () {
    Permission::factory()->create(['name' => 'view_users', 'module' => 'User']);
    Permission::factory()->create(['name' => 'edit_users', 'module' => 'User']);
    Permission::factory()->create(['name' => 'view_posts', 'module' => 'Post']);

    $all = $this->service->list();
    expect($all)->toHaveCount(3);

    $userModule = $this->service->list(['module' => 'User']);
    expect($userModule)->toHaveCount(2);

    $search = $this->service->list(['search' => 'posts']);
    expect($search)->toHaveCount(1);
});

test('it can create, update and delete permission', function () {
    $permission = $this->service->create([
        'name' => 'create_roles',
        'guard_name' => 'web',
        'module' => 'Permission',
    ]);

    expect($permission->name)->toBe('create_roles');

    $this->service->update($permission->id, ['name' => 'new_name']);
    expect($permission->refresh()->name)->toBe('new_name');

    $this->service->delete($permission->id);
    expect(Permission::find($permission->id))->toBeNull();
});
