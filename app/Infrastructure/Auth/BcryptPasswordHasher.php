<?php

namespace App\Infrastructure\Auth;

use App\Auth\Application\Service\PasswordHasherInterface;

class BcryptPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        return bcrypt($password);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
