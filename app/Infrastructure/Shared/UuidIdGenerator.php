<?php

namespace App\Infrastructure\Shared;

use App\Shared\Application\Service\IdGeneratorInterface;

class UuidIdGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }
}
