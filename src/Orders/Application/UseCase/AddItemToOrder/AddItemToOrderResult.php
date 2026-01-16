<?php

namespace App\Orders\Application\UseCase\AddItemToOrder;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the result of successfully adding an item to an order.
 * Ensures the handler returns data in a type-safe manner.
 *
 * WHAT IT DOES:
 * Holds the order ID confirming the item was added to it.
 */
final class AddItemToOrderResult
{
    public function __construct(
        private readonly int $orderId,
    ) {
    }

    public function orderId(): int
    {
        return $this->orderId;
    }
}
