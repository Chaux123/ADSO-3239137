<?php

namespace App\Providers;

use App\Adapters\DatabaseLoanAdapter;
use App\Ports\LoanPort;
use App\Ports\FamilyPort;
use App\Adapters\FamilyRepository;
use App\Ports\PromoterPort;
use App\Adapters\PromoterRepository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        $this->app->bind(FamilyPort::class, FamilyRepository::class);
        $this->app->bind(PromoterPort::class, PromoterRepository::class);
        $this->app->bind(
            LoanPort::class,
            DatabaseLoanAdapter::class
        );
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        //
    }
}
