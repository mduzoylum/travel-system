<?php

namespace App\Observers;

use App\DDD\Modules\Reservation\Models\Reservation;
use Illuminate\Support\Facades\Log;

class ReservationObserver
{
    /**
     * Handle the Reservation "creating" event.
     *
     * @param  \App\DDD\Modules\Reservation\Models\Reservation  $reservation
     * @return void
     */
    public function creating(Reservation $reservation)
    {
        // Rezervasyon oluşturulurken otelin muhasebe kodunu al
        if (!$reservation->accounting_code && $reservation->room) {
            $contract = $reservation->room->contract;
            if ($contract && $contract->hotel) {
                $hotel = $contract->hotel;
                if ($hotel->accounting_code) {
                    $reservation->accounting_code = $hotel->accounting_code;
                    
                    Log::info('Rezervasyon oluşturuldu - Muhasebe kodu otomatik atandı', [
                        'reservation_id' => $reservation->id,
                        'hotel_id' => $hotel->id,
                        'hotel_name' => $hotel->name,
                        'accounting_code' => $hotel->accounting_code
                    ]);
                }
            }
        }
    }
}
