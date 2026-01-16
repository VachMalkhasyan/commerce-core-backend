<?php

namespace App\Auth\Domain\ValueObject;

use DomainException;

final class PasswordHash
{
    private string $hash;

    public function __construct(string $hash)
    {
        if ($hash === '') {
            throw new DomainException('Password hash cannot be empty');
        }

        $this->hash = $hash;
    }

    public function value(): string
    {
        return $this->hash;
    }

    public function equals(self $other): bool
    {
        return $this->hash === $other->hash;
    }
}
