<?php

namespace Modules\Setting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Setting\Models\Setting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->slug,
            'value' => $this->faker->sentence,
            'type' => 'string',
            'description' => $this->faker->paragraph,
            'group' => $this->faker->word,
        ];
    }
}
