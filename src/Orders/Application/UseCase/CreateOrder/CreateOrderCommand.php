<?php

namespace App\Orders\Application\UseCase\CreateOrder;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the CreateOrder command, capturing the intent
 * to create a new order for a user. Ensures type safety and immutability.
 *
 * WHAT IT DOES:
 * Holds the user ID who is creating the order.
 */
final class CreateOrderCommand
{
    public function __construct(
        private readonly int $userId,
    ) {
    }

    public function userId(): int
    {
        return $this->userId;
    }
}
