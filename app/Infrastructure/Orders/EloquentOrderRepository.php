<?php

namespace App\Infrastructure\Orders;

use App\Models\Order;
use App\Orders\Domain\Entity\Order as OrderEntity;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function save(OrderEntity $order): void
    {
        Order::updateOrCreate(
            ['id' => $order->getId()],
            [
                'user_id' => $order->getUserId(),
                'status' => $order->getStatus()->value,
            ]
        );
    }

    public function getById(int $id): ?OrderEntity
    {
        $model = Order::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    private function toDomain(Order $model): OrderEntity
    {
        return new OrderEntity(
            $model->id,
            $model->user_id,
            $model->status,
            $model->items
        );
    }
}
