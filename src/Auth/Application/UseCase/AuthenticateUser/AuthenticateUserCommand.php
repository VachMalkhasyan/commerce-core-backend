<?php

namespace App\Auth\Application\UseCase\AuthenticateUser;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the AuthenticateUser command, capturing the intent
 * to authenticate a user with credentials. Ensures type safety and immutability.
 *
 * WHAT IT DOES:
 * Holds the email and plain password to verify during authentication.
 */
final class AuthenticateUserCommand
{
    public function __construct(
        private readonly string $email,
        private readonly string $plainPassword,
    ) {
    }

    public function email(): string
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }
}
