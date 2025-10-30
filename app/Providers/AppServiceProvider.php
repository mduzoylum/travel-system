<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\Observers\SupplierObserver;
use App\Observers\ReservationObserver;
use Carbon\Carbon;

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
        // Timezone'u Türkiye saati olarak ayarla
        Carbon::setLocale('tr');
        date_default_timezone_set(config('app.timezone'));
        
        // Observer'ları kaydet
        Supplier::observe(SupplierObserver::class);
        Reservation::observe(ReservationObserver::class);
    }
}
