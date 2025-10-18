<?php

namespace App\Observers;

use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use Illuminate\Support\Facades\Log;

class SupplierObserver
{
    /**
     * Handle the Supplier "updating" event.
     *
     * @param  \App\DDD\Modules\Supplier\Domain\Entities\Supplier  $supplier
     * @return void
     */
    public function updating(Supplier $supplier)
    {
        // Muhasebe kodu değişti mi kontrol et
        if ($supplier->isDirty('accounting_code')) {
            $oldCode = $supplier->getOriginal('accounting_code');
            $newCode = $supplier->accounting_code;
            
            Log::info('Tedarikçi muhasebe kodu değiştirildi', [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'old_code' => $oldCode,
                'new_code' => $newCode
            ]);
        }
    }

    /**
     * Handle the Supplier "updated" event.
     *
     * @param  \App\DDD\Modules\Supplier\Domain\Entities\Supplier  $supplier
     * @return void
     */
    public function updated(Supplier $supplier)
    {
        // Muhasebe kodu değişmişse ilgili kayıtları güncelle
        if ($supplier->wasChanged('accounting_code')) {
            $newCode = $supplier->accounting_code;
            
            // 1. Bu tedarikçiye bağlı tüm otellerin muhasebe kodunu güncelle
            $hotelsUpdated = $supplier->hotels()->update([
                'accounting_code' => $newCode
            ]);
            
            // 2. Bu tedarikçiye bağlı otellerin rezervasyonlarının muhasebe kodunu güncelle
            $hotelIds = $supplier->hotels()->pluck('id')->toArray();
            
            if (!empty($hotelIds)) {
                $reservationsUpdated = \App\DDD\Modules\Reservation\Models\Reservation::whereHas('room', function($query) use ($hotelIds) {
                    $query->whereHas('contract', function($q) use ($hotelIds) {
                        $q->whereIn('hotel_id', $hotelIds);
                    });
                })->update([
                    'accounting_code' => $newCode
                ]);
                
                Log::info('Tedarikçi muhasebe kodu güncellendi - İlgili kayıtlar güncellendi', [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'new_code' => $newCode,
                    'hotels_updated' => $hotelsUpdated,
                    'reservations_updated' => $reservationsUpdated
                ]);
            } else {
                Log::info('Tedarikçi muhasebe kodu güncellendi - Sadece oteller güncellendi', [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'new_code' => $newCode,
                    'hotels_updated' => $hotelsUpdated
                ]);
            }
        }
    }
}
