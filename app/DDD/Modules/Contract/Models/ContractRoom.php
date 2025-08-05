<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class ContractRoom extends Model
{
    protected $fillable = [
        'contract_id', 'room_type', 'meal_plan', 'base_price', 'sale_price'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function getCommissionAttribute(): float
    {
        return $this->sale_price - $this->base_price;
    }

    public function getCommissionRateAttribute(): float
    {
        return round(($this->commission / $this->sale_price) * 100, 2);
    }

    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class);
    }
}
