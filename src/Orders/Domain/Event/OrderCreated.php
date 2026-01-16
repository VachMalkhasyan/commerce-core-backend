<?php

namespace App\Orders\Domain\Event;

final class OrderCreated
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $userId
    ) {}
}
