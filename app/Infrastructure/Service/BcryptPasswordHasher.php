<?php

namespace App\Infrastructure\Service;

use App\Auth\Domain\Service\PasswordHasherInterface;

class BcryptPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
