<?php

namespace App\Orders\Application\UseCase\ConfirmOrder;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the ConfirmOrder command, capturing the intent
 * to confirm and finalize an order. Ensures type safety and immutability.
 *
 * WHAT IT DOES:
 * Holds the order ID of the order to be confirmed.
 */
final class ConfirmOrderCommand
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
