<?php

declare(strict_types=1);

namespace Modules\Internship\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InternshipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Internship\Models\Internship::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
