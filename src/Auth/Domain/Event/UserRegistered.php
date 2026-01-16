<?php

namespace App\Auth\Domain\Event;

final class UserRegistered
{
    public function __construct(
        public readonly int $userId,
        public readonly string $email
    ) {}
}
