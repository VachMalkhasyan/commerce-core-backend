<?php

namespace App\Orders\Domain\Entity;

use App\Orders\Domain\Enum\OrderStatus;
use App\Orders\Domain\ValueObject\OrderItem;
use App\Orders\Domain\Exception\EmptyOrderException;
use App\Orders\Domain\Exception\InvalidOrderStateException;
use App\Orders\Domain\Event\OrderCreated;
use App\Orders\Domain\Event\OrderConfirmed;

final class Order
{
    private int $id;
    private int $userId;
    private OrderStatus $status;
    private array $items = [];
    private array $events = [];

    private function __construct(int $id, int $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->status = OrderStatus::DRAFT;
    }

    public static function create(int $id, int $userId): self
    {
        $order = new self($id, $userId);
        $order->recordEvent(new OrderCreated($id, $userId));

        return $order;
    }

    public function addItem(OrderItem $item): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new InvalidOrderStateException('Cannot modify confirmed order');
        }

        $this->items[] = $item;
    }

    public function confirm(): void
    {
        if (empty($this->items)) {
            throw new EmptyOrderException('Order must contain at least one item');
        }

        if ($this->status !== OrderStatus::DRAFT) {
            throw new InvalidOrderStateException('Order already confirmed or cancelled');
        }

        $this->status = OrderStatus::CONFIRMED;
        $this->recordEvent(new OrderConfirmed($this->id));
    }

    public function totalAmount(): int
    {
        return array_sum(
            array_map(fn (OrderItem $item) => $item->total(), $this->items)
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function events(): array
    {
        return $this->events;
    }

    private function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }
}
