<?php

namespace App\Auth\Domain\Entity;

use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\PasswordHash;
use App\Auth\Domain\Event\UserRegistered;

final class User
{
    private int $id;
    private Email $email;
    private PasswordHash $passwordHash;
    private bool $isActive;

    private array $events = [];

    private function __construct(
        int $id,
        Email $email,
        PasswordHash $passwordHash,
        bool $isActive
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->isActive = $isActive;
    }

    public static function register(
        int $id,
        Email $email,
        PasswordHash $passwordHash
    ): self {
        $user = new self(
            $id,
            $email,
            $passwordHash,
            true
        );

        $user->recordEvent(new UserRegistered($id, $email->value()));

        return $user;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): PasswordHash
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function events(): array
    {
        return $this->events;
    }

    private function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }
}
