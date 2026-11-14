
<?php

namespace App\Providers;

use App\Adapters\DatabaseLoanAdapter;
use App\Ports\LoanPort;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
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
