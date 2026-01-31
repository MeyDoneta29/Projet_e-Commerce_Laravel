<?php

namespace Database\Factories;

use App\Models\cart;
use App\Models\product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CartItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cart_id' => cart::factory(),
            'product_id' => product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
