<?php

namespace App\Auth\Application\UseCase\RegisterUser;

use App\Auth\Application\Service\PasswordHasherInterface;
use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Exception\UserAlreadyExistsException;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\PasswordHash;
use App\Shared\Application\Service\IdGeneratorInterface;

/**
 * WHY IT EXISTS:
 * Central orchestrator for the RegisterUser use case. Handles the business flow:
 * validates email uniqueness, hashes the password, creates a User via Domain,
 * and persists it. Coordinates Domain objects and Repository interfaces without
 * imposing infrastructure concerns.
 *
 * WHAT IT DOES:
 * 1. Validates the email is not already registered
 * 2. Generates a unique user ID
 * 3. Creates Email and PasswordHash value objects (Domain validation)
 * 4. Uses Domain factory method to create User with proper state
 * 5. Persists User via Repository
 * 6. Returns a RegisterUserResult with the registered user ID
 */
final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly IdGeneratorInterface $idGenerator,
    ) {
    }

    /**
     * Execute the RegisterUser use case.
     *
     * @throws UserAlreadyExistsException If email is already registered
     * @throws \DomainException If email or password is invalid
     */
    public function handle(RegisterUserCommand $command): RegisterUserResult
    {
        // Validate email uniqueness by querying repository
        $email = new Email($command->email());
        
        if ($this->userRepository->findByEmail($email) !== null) {
            throw new UserAlreadyExistsException(
                sprintf('User with email "%s" already exists', $email->value())
            );
        }

        // Generate unique user ID
        $userId = $this->idGenerator->generate();

        // Hash the plain password via abstracted service
        $hashedPassword = $this->passwordHasher->hash($command->plainPassword());

        // Create value objects (Domain validates)
        $passwordHash = new PasswordHash($hashedPassword);

        // Use Domain factory to create User with proper state and events
        $user = User::register($userId, $email, $passwordHash);

        // Persist User via Repository
        $this->userRepository->save($user);

        // Return result with registered user ID
        return new RegisterUserResult($userId);
    }
}
