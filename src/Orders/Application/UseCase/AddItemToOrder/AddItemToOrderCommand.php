<?php

namespace App\Orders\Application\UseCase\AddItemToOrder;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the AddItemToOrder command, capturing the intent
 * to add a product item to an existing order. Ensures type safety and immutability.
 *
 * WHAT IT DOES:
 * Holds the order ID and item details (product ID, quantity, unit price).
 */
final class AddItemToOrderCommand
{
    public function __construct(
        private readonly int $orderId,
        private readonly int $productId,
        private readonly int $quantity,
        private readonly int $unitPrice,
    ) {
    }

    public function orderId(): int
    {
        return $this->orderId;
    }

    public function productId(): int
    {
        return $this->productId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): int
    {
        return $this->unitPrice;
    }
}
