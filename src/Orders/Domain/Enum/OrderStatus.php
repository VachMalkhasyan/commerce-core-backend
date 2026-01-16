<?php

namespace App\Orders\Domain\Enum;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
