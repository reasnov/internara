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
     * This default state is a string setting.
     */
    public function definition(): array
    {
        return $this->buildStateByType(default: $this->faker->sentence());
    }

    /**
     * Indicate that the setting is of type string.
     */
    public function string(?string $key = null, ?string $value = null, ?string $description = null, ?string $group = null): self
    {
        return $this->state(fn (array $attributes) => $this->buildStateByType(
            'string',
            $key,
            $value,
            $this->faker->sentence(),
            $description,
            $group
        ));
    }

    /**
     * Indicate that the setting is of type integer.
     */
    public function integer(?string $key = null, ?int $value = null, ?string $description = null, ?string $group = null): self
    {
        return $this->state(fn (array $attributes) => $this->buildStateByType(
            'integer',
            $key,
            $value,
            $this->faker->numberBetween(1, 100),
            $description,
            $group
        ));
    }

    /**
     * Indicate that the setting is of type boolean.
     */
    public function boolean(?string $key = null, ?bool $value = null, ?string $description = null, ?string $group = null): self
    {
        return $this->state(fn (array $attributes) => $this->buildStateByType(
            'boolean',
            $key,
            $value,
            $this->faker->boolean(),
            $description,
            $group
        ));
    }

    /**
     * Indicate that the setting is of type array.
     */
    public function array(?string $key = null, ?array $value = null, ?string $description = null, ?string $group = null): self
    {
        return $this->state(fn (array $attributes) => $this->buildStateByType(
            'array',
            $key,
            $value,
            ['item1' => $this->faker->word(), 'item2' => $this->faker->word()],
            $description,
            $group
        ));
    }

    /**
     * Indicate that the setting is of type json.
     */
    public function json(?string $key = null, ?array $value = null, ?string $description = null, ?string $group = null): self
    {
        return $this->state(fn (array $attributes) => $this->buildStateByType(
            'json',
            $key,
            $value,
            ['data_key' => $this->faker->word(), 'data_value' => $this->faker->sentence()],
            $description,
            $group
        ));
    }

    /**
     * Build a state array for a setting of a specific type.
     */
    protected function buildStateByType(string $type = 'string', ?string $key = null, mixed $value = null, mixed $default = null, ?string $description = null, ?string $group = null): array
    {
        return [
            'key' => $key ?? $this->faker->unique()->slug(2),
            'value' => $value ?? $default,
            'type' => $type,
            'description' => $description ?? $this->faker->paragraph(),
            'group' => $group ?? $this->faker->word(),
        ];
    }
}
