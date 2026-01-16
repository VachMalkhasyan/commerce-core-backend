<?php

namespace App\Auth\Application\UseCase\AuthenticateUser;

use App\Auth\Application\Service\PasswordHasherInterface;
use App\Auth\Domain\Exception\InvalidCredentialsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\Email;

/**
 * WHY IT EXISTS:
 * Central orchestrator for the AuthenticateUser use case. Handles the business flow:
 * looks up a user by email, verifies the password hash, and returns authentication result.
 * Enforces Domain rule that credentials must match exactly.
 *
 * WHAT IT DOES:
 * 1. Constructs Email value object to validate email format
 * 2. Queries Repository to find user by email
 * 3. Throws InvalidCredentialsException if user not found
 * 4. Verifies plain password against stored hash via PasswordHasher
 * 5. Throws InvalidCredentialsException if password does not match
 * 6. Returns AuthenticateUserResult with authenticated user ID
 */
final class AuthenticateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * Execute the AuthenticateUser use case.
     *
     * @throws InvalidCredentialsException If user not found or password does not match
     * @throws \DomainException If email format is invalid
     */
    public function handle(AuthenticateUserCommand $command): AuthenticateUserResult
    {
        // Create Email value object (Domain validates format)
        $email = new Email($command->email());

        // Query Repository to find user by email
        $user = $this->userRepository->findByEmail($email);

        // Domain rule: user must exist
        if ($user === null) {
            throw new InvalidCredentialsException('Invalid email or password');
        }

        // Verify password via abstracted PasswordHasher
        // We need to get password hash from user - this requires accessing it
        // For this, we'd need a getter on User entity. Assuming it exists:
        // Note: The Domain entity needs to expose password hash verification capability
        // For now, we verify by comparing hashes
        if (!$this->passwordHasher->verify($command->plainPassword(), $user->getPasswordHash()->value())) {
            throw new InvalidCredentialsException('Invalid email or password');
        }

        // Return result with authenticated user ID
        return new AuthenticateUserResult($user->getId());
    }
}
