<?php

namespace App\Orders\Application\UseCase\CreateOrder;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the result of successful order creation.
 * Ensures the handler returns data in a type-safe manner.
 *
 * WHAT IT DOES:
 * Holds the ID of the newly created order.
 */
final class CreateOrderResult
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
