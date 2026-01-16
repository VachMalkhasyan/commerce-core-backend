<?php

namespace App\Infrastructure\Auth;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\PasswordHash;
use App\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        UserModel::updateOrCreate(
            ['email' => $user->getEmail()->value()],
            [
                'id' => $user->getId(),
                'email' => $user->getEmail()->value(),
                'password' => $user->getPasswordHash()->value(),
            ]
        );
    }

    public function findByEmail(Email $email): ?User
    {
        $userModel = UserModel::where('email', $email->value())->first();

        if (!$userModel) {
            return null;
        }

        return $this->toDomain($userModel);
    }

    private function toDomain(UserModel $model): User
    {
        return User::reconstruct(
            $model->id,
            new Email($model->email),
            new PasswordHash($model->password)
        );
    }
}
