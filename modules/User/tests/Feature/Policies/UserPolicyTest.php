<?php

namespace Modules\User\Tests\Feature\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Permission\Models\Permission;
use Modules\Permission\Models\Role;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup basic roles and permissions for testing
    $this->adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $this->viewPermission = Permission::create(['name' => 'user.view', 'guard_name' => 'web']);
    $this->managePermission = Permission::create(['name' => 'user.manage', 'guard_name' => 'web']);

    $this->adminRole->givePermissionTo($this->viewPermission);
});

test('admin can view user list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    expect(Gate::forUser($admin)->allows('viewAny', User::class))->toBeTrue();
});

test('regular user cannot view user list', function () {
    $user = User::factory()->create();

    expect(Gate::forUser($user)->allows('viewAny', User::class))->toBeFalse();
});

test('user can view their own profile', function () {
    $user = User::factory()->create();

    expect(Gate::forUser($user)->allows('view', $user))->toBeTrue();
});

test('user cannot view others profile without permission', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    expect(Gate::forUser($user)->allows('view', $other))->toBeFalse();
});

test('user with manage permission can view others profile', function () {
    $manager = User::factory()->create();
    // Spatie trait masih bisa dipakai di test karena test memang butuh trigger data
    $manager->givePermissionTo('user.manage');
    $other = User::factory()->create();

    // Pastikan checking menggunakan standar Gate Laravel
    expect($manager->can('view', $other))->toBeTrue();
});
