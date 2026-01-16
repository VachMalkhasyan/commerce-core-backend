<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['DRAFT', 'CONFIRMED', 'PAID', 'CANCELLED']),
            'total_amount' => fake()->randomFloat(2, 50, 2000),
        ];
    }

    /**
     * Indicate that the order should be in DRAFT status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'DRAFT',
            'total_amount' => null,
        ]);
    }

    /**
     * Indicate that the order should be in CONFIRMED status.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'CONFIRMED',
        ]);
    }

    /**
     * Indicate that the order should be in PAID status.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'PAID',
        ]);
    }

    /**
     * Indicate that the order should be in CANCELLED status.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'CANCELLED',
        ]);
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($order) {
            // Automatically create order items after creating an order
            if ($order->status !== 'DRAFT') {
                $itemCount = fake()->numberBetween(1, 5);
                OrderItemFactory::new()->count($itemCount)->create(['order_id' => $order->id]);
            }
        });
    }
}
