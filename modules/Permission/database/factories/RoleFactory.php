<?php

namespace Modules\Permission\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Permission\Models\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word,
            'guard_name' => 'web',
            'module' => fake()->word(),
        ];
    }
}
