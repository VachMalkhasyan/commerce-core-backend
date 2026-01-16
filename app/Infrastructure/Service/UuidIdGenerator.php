<?php

namespace App\Infrastructure\Service;

use App\Shared\Application\Service\IdGeneratorInterface;
use Illuminate\Support\Str;

class UuidIdGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        return Str::uuid()->toString();
    }
}
