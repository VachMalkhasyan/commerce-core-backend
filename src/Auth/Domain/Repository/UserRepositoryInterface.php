<?php

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\ValueObject\Email;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findByEmail(Email $email): ?User;
}
