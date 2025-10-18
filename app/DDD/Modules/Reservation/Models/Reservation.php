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
}
