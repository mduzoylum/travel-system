<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'hotel_id',
        'reservation_id',
        'amount',
        'currency',
        'payment_type',
        'due_date',
        'status',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'amount' => 'decimal:2'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservation()
    {
        return $this->belongsTo(\App\DDD\Modules\Reservation\Models\Reservation::class);
    }
}
