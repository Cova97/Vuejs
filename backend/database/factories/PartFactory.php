<?php

namespace Database\Factories;

use App\Models\Part;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Part>
 */
class PartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => fake()->unique()->bothify('PZ-####-??'),
            'name' => ucfirst(fake()->words(2, true)),
            'description' => fake()->optional()->sentence(),
            'quantity' => fake()->numberBetween(0, 500),
            'price' => fake()->randomFloat(2, 1, 2000),
            'active' => fake()->boolean(85),
        ];
    }

    /**
     * Estado opcional: una pieza inactiva.
     * Uso: Part::factory()->inactive()->create();
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
