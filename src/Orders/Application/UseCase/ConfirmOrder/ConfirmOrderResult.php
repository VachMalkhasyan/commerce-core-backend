<?php

namespace App\Orders\Application\UseCase\ConfirmOrder;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the result of successfully confirming an order.
 * Ensures the handler returns data in a type-safe manner and prevents accidental mutation.
 *
 * WHAT IT DOES:
 * Holds the confirmed order ID and the total order amount for confirmation receipt.
 */
final class ConfirmOrderResult
{
    public function __construct(
        private readonly int $orderId,
        private readonly int $totalAmount,
    ) {
    }

    public function orderId(): int
    {
        return $this->orderId;
    }

    public function totalAmount(): int
    {
        return $this->totalAmount;
    }
}
