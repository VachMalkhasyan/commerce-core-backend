<?php

namespace App\Orders\Domain\Repository;

use App\Orders\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function getById(int $id): ?Order;
}
