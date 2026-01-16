<?php

namespace App\Orders\Domain\ValueObject;

use DomainException;

final class OrderItem
{
    private int $productId;
    private int $quantity;
    private int $unitPrice;

    public function __construct(int $productId, int $quantity, int $unitPrice)
    {
        if ($quantity <= 0) {
            throw new DomainException('Quantity must be greater than zero');
        }

        if ($unitPrice <= 0) {
            throw new DomainException('Unit price must be greater than zero');
        }

        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function total(): int
    {
        return $this->quantity * $this->unitPrice;
    }

    public function productId(): int
    {
        return $this->productId;
    }
}
