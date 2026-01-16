<?php

namespace App\Orders\Domain\Event;

final class OrderConfirmed
{
    public function __construct(
        public readonly int $orderId
    ) {}
}
