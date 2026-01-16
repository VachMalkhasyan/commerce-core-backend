<?php

namespace App\Providers;

use App\Auth\Application\Service\PasswordHasherInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Auth\BcryptPasswordHasher;
use App\Infrastructure\Auth\EloquentUserRepository;
use App\Infrastructure\Orders\EloquentOrderRepository;
use App\Infrastructure\Shared\UuidIdGenerator;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Shared\Application\Service\IdGeneratorInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->singleton(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->singleton(OrderRepositoryInterface::class, EloquentOrderRepository::class);

        // Bind service interfaces to implementations
        $this->app->singleton(PasswordHasherInterface::class, BcryptPasswordHasher::class);
        $this->app->singleton(IdGeneratorInterface::class, UuidIdGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
