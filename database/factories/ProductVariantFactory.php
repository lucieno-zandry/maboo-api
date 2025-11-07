<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->colorName(),
            'price' => fake()->numberBetween(100, 300),
            'inStock' => fake()->numberBetween(0, 50),
            'product_id' => fake()->numberBetween(1, 200),
        ];
    }
}
