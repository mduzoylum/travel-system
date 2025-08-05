<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    protected $fillable = [
        'contract_room_id', 'date', 'stock',
    ];

    public function room()
    {
        return $this->belongsTo(ContractRoom::class, 'contract_room_id');
    }

    public function scopeAvailableOn($query, string $date)
    {
        return $query->where('date', $date)->where('stock', '>', 0);
    }
}
