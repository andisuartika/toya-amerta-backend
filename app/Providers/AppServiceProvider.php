<?php

namespace App\Providers;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\Contracts\TariffRateRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Infrastructure\Repositories\CustomerRepository;
use App\Infrastructure\Repositories\TariffRateRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\ZoneRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(ZoneRepositoryInterface::class,       ZoneRepository::class);
        $this->app->bind(TariffRateRepositoryInterface::class, TariffRateRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class,   CustomerRepository::class);
        $this->app->bind(UserRepositoryInterface::class,       UserRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
