<?php

namespace App\Auth\Application\UseCase\AuthenticateUser;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the result of successful user authentication.
 * Ensures the handler returns data in a type-safe manner.
 *
 * WHAT IT DOES:
 * Holds the ID of the authenticated user.
 */
final class AuthenticateUserResult
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
