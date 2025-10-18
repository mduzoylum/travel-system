<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\Observers\SupplierObserver;
use App\Observers\ReservationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer'ları kaydet
        Supplier::observe(SupplierObserver::class);
        Reservation::observe(ReservationObserver::class);
    }
}
