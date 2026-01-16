<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 10, 500);
        $quantity = fake()->numberBetween(1, 10);
        $subtotal = round($unitPrice * $quantity, 2);

        return [
            'product_id' => 'PROD-'.fake()->numberBetween(1000, 9999),
            'product_name' => fake()->words(2, true),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ];
    }

    /**
     * Set specific quantity.
     */
    public function withQuantity(int $quantity): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $quantity,
            'subtotal' => round($attributes['unit_price'] * $quantity, 2),
        ]);
    }

    /**
     * Set specific price.
     */
    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_price' => $price,
            'subtotal' => round($price * $attributes['quantity'], 2),
        ]);
    }
}
