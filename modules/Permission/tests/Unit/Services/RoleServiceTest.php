<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Permission\Models\Permission;
use Modules\Permission\Models\Role;
use Modules\Permission\Services\RoleService;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new RoleService(new Role);
});

test('it can list and filter roles', function () {
    Role::factory()->create(['name' => 'admin', 'module' => 'Core']);
    Role::factory()->create(['name' => 'editor', 'module' => 'Content']);

    $results = $this->service->list(['module' => 'Core']);
    expect($results)->toHaveCount(1);
    expect($results->items()[0]->name)->toBe('admin');
});

test('it can create, update and delete role', function () {
    $role = $this->service->create([
        'name' => 'manager',
        'guard_name' => 'web',
        'module' => 'Core',
    ]);

    expect($role->name)->toBe('manager');

    $this->service->update($role->id, ['name' => 'supervisor']);
    expect($role->refresh()->name)->toBe('supervisor');

    $this->service->delete($role->id);
    expect(Role::find($role->id))->toBeNull();
});

test('it can sync permissions to role', function () {
    $role = Role::factory()->create();
    $p1 = Permission::factory()->create(['name' => 'p1']);
    $p2 = Permission::factory()->create(['name' => 'p2']);

    $this->service->syncPermissions($role->id, ['p1', 'p2']);

    expect($role->permissions)->toHaveCount(2);
    expect($role->hasPermissionTo('p1'))->toBeTrue();
    expect($role->hasPermissionTo('p2'))->toBeTrue();
});
