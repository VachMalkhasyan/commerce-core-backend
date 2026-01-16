<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create additional users
        $users = User::factory(5)->create();

        // Create orders for test user
        $testUserOrders = [
            // Draft order
            Order::factory()->draft()->create([
                'user_id' => $testUser->id,
                'total_amount' => null,
            ])->each(function ($order) {
                OrderItem::factory(2)->create(['order_id' => $order->id]);
            }),

            // Confirmed order
            Order::factory()->confirmed()->create([
                'user_id' => $testUser->id,
                'total_amount' => 1250.50,
            ])->each(function ($order) {
                OrderItem::factory(3)->create(['order_id' => $order->id]);
            }),

            // Paid order
            Order::factory()->paid()->create([
                'user_id' => $testUser->id,
                'total_amount' => 3450.75,
            ])->each(function ($order) {
                OrderItem::factory(4)->create(['order_id' => $order->id]);
            }),
        ];

        // Create orders for other users
        foreach ($users as $user) {
            // 2-5 orders per user
            $orderCount = fake()->numberBetween(2, 5);

            Order::factory($orderCount)
                ->confirmed()
                ->create(['user_id' => $user->id])
                ->each(function ($order) {
                    OrderItem::factory(fake()->numberBetween(1, 5))->create(['order_id' => $order->id]);
                });
        }
    }
}
