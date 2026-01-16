<?php

namespace App\Auth\Application\UseCase\RegisterUser;

/**
 * WHY IT EXISTS:
 * Immutable DTO representing the RegisterUser command, capturing the intent
 * to register a new user. Ensures the handler receives complete, validated data
 * in a type-safe manner.
 *
 * WHAT IT DOES:
 * Holds the email and plain password from the user input.
 * Prevents modification after creation, ensuring data integrity throughout the use case.
 */
final class RegisterUserCommand
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
