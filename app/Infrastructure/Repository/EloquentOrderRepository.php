<?php

namespace App\Infrastructure\Repository;

use App\Models\Order as OrderModel;
use App\Orders\Domain\Entity\Order;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findById(string $id): ?Order
    {
        $orderModel = OrderModel::with('items')->find($id);

        if (! $orderModel) {
            return null;
        }

        return $this->toDomainEntity($orderModel);
    }

    public function save(Order $order): void
    {
        $orderModel = OrderModel::updateOrCreate(
            ['id' => $order->getId()],
            [
                'user_id' => $order->getUserId(),
                'status' => $order->getStatus()->value,
                'total' => $order->calculateTotal(),
                'created_at' => now(),
            ]
        );

        // Sync order items
        $orderModel->items()->delete();
        foreach ($order->getItems() as $item) {
            $orderModel->items()->create([
                'product_name' => $item->getProductName(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
            ]);
        }
    }

    private function toDomainEntity(OrderModel $orderModel): Order
    {
        $order = new Order(
            $orderModel->id,
            $orderModel->user_id,
            $orderModel->created_at
        );

        // Set status via reflection
        $reflection = new \ReflectionClass($order);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($order, $orderModel->status);

        // Set items via reflection
        $itemsProperty = $reflection->getProperty('items');
        $itemsProperty->setAccessible(true);
        $items = $orderModel->items->map(function ($item) {
            return new \App\Orders\Domain\ValueObject\OrderItem(
                $item->product_name,
                $item->quantity,
                $item->price
            );
        })->toArray();
        $itemsProperty->setValue($order, $items);

        return $order;
    }
}
