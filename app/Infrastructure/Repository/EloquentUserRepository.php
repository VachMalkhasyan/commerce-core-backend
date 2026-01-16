<?php

namespace App\Infrastructure\Repository;

use App\Auth\Domain\Entity\User;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\Email;
use App\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(Email $email): ?User
    {
        $userModel = UserModel::where('email', $email->value())->first();

        if (! $userModel) {
            return null;
        }

        return $this->toDomainEntity($userModel);
    }

    public function save(User $user): void
    {
        $userModel = UserModel::updateOrCreate(
            ['id' => $user->getId()],
            [
                'email' => $user->getEmail()->value(),
                'password' => $user->getPasswordHash()->value(),
                'created_at' => now(),
            ]
        );
    }

    public function findById(string $id): ?User
    {
        $userModel = UserModel::find($id);

        if (! $userModel) {
            return null;
        }

        return $this->toDomainEntity($userModel);
    }

    private function toDomainEntity(UserModel $userModel): User
    {
        $user = new User(
            $userModel->id,
            new Email($userModel->email),
            $userModel->created_at
        );

        // Set password hash via reflection since the domain entity doesn't expose a setter
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('passwordHash');
        $property->setAccessible(true);
        $property->setValue($user, $userModel->password);

        return $user;
    }
}
