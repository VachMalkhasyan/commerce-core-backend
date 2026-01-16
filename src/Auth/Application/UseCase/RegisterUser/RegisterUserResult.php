<?php

namespace App\Auth\Application\UseCase\RegisterUser;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the result of successful user registration.
 * Ensures the handler returns data in a type-safe manner and prevents accidental
 * mutation of the result after creation.
 *
 * WHAT IT DOES:
 * Holds the ID of the newly registered user.
 */
final class RegisterUserResult
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
