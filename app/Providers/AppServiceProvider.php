<?php

namespace App\Providers;

use App\Domain\Contracts\CustomerRepositoryInterface;
use App\Domain\Contracts\MasterMeterReadingRepositoryInterface;
use App\Domain\Contracts\MeterReplacementRepositoryInterface;
use App\Domain\Contracts\TariffRateRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Contracts\MaintenanceRepositoryInterface;
use App\Domain\Contracts\PaymentRepositoryInterface;
use App\Domain\Contracts\WaterReadingRepositoryInterface;
use App\Domain\Contracts\ZoneRepositoryInterface;
use App\Infrastructure\Repositories\CustomerRepository;
use App\Infrastructure\Repositories\MaintenanceRepository;
use App\Infrastructure\Repositories\MasterMeterReadingRepository;
use App\Infrastructure\Repositories\MeterReplacementRepository;
use App\Infrastructure\Repositories\TariffRateRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Repositories\PaymentRepository;
use App\Infrastructure\Repositories\WaterReadingRepository;
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
        $this->app->bind(UserRepositoryInterface::class,        UserRepository::class);
        $this->app->bind(WaterReadingRepositoryInterface::class,  WaterReadingRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class,       PaymentRepository::class);
        $this->app->bind(MaintenanceRepositoryInterface::class,   MaintenanceRepository::class);
        $this->app->bind(MasterMeterReadingRepositoryInterface::class, MasterMeterReadingRepository::class);
        $this->app->bind(MeterReplacementRepositoryInterface::class, MeterReplacementRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
