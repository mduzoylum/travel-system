<?php

namespace App\DDD\Modules\Reservation\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id', 'contract_room_id', 'checkin_date', 'checkout_date',
        'guest_count', 'total_price', 'accounting_code', 'status'
    ];

    public function room()
    {
        return $this->belongsTo(\App\DDD\Modules\Contract\Models\ContractRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function supplier()
    {
        return $this->hasOneThrough(
            \App\DDD\Modules\Supplier\Domain\Entities\Supplier::class,
            \App\DDD\Modules\Contract\Models\ContractRoom::class,
            'id', // contract_rooms.id
            'id', // suppliers.id
            'contract_room_id', // reservations.contract_room_id
            'contract_id' // contract_rooms.contract_id
        )->join('contracts', 'contract_rooms.contract_id', '=', 'contracts.id')
         ->join('hotels', 'contracts.hotel_id', '=', 'hotels.id')
         ->join('suppliers', 'hotels.supplier_id', '=', 'suppliers.id')
         ->select('suppliers.*');
    }

    /**
     * Silinmiş tedarikçi kontrolü
     */
    public function hasDeletedSupplier(): bool
    {
        $supplier = $this->supplier;
        return $supplier && $supplier->trashed();
    }

    /**
     * Tedarikçi adını silinmiş durumuna göre getir
     */
    public function getSupplierNameAttribute(): string
    {
        $supplier = $this->supplier;
        if (!$supplier) {
            return 'Tedarikçi Bulunamadı';
        }
        
        return $supplier->trashed() ? $supplier->name . ' (Silinmiş)' : $supplier->name;
    }
}
