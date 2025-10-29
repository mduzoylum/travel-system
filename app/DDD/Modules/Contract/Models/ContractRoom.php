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

    public function periods()
    {
        return $this->hasMany(ContractRoomPeriod::class);
    }

    /**
     * Belirtilen tarih için geçerli periyodu getir
     */
    public function getPeriodForDate(\Carbon\Carbon $date): ?ContractRoomPeriod
    {
        return $this->periods()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Belirtilen tarih aralığındaki tüm periyotları getir
     */
    public function getPeriodsForDateRange(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate)
    {
        return $this->periods()
            ->where('is_active', true)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->get();
    }
}
