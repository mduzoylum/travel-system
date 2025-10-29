<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ContractRoomPeriod extends Model
{
    protected $fillable = [
        'contract_room_id', 'start_date', 'end_date', 'currency',
        'base_price', 'sale_price', 'notes', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function contractRoom()
    {
        return $this->belongsTo(ContractRoom::class);
    }

    /**
     * Belirtilen tarih bu periyot içinde mi?
     */
    public function containsDate(Carbon $date): bool
    {
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Bu periyot diğer bir periyotla çakışıyor mu?
     */
    public function overlaps(ContractRoomPeriod $other): bool
    {
        return $this->start_date->lt($other->end_date) && 
               $this->end_date->gt($other->start_date);
    }

    /**
     * Geçerli periyot mu?
     */
    public function isValid(): bool
    {
        return $this->is_active && 
               $this->start_date->lte($this->end_date);
    }
}
