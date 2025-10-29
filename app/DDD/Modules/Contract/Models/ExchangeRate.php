<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExchangeRate extends Model
{
    protected $fillable = [
        'from_currency', 'to_currency', 'rate',
        'valid_from', 'valid_until', 'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Belirtilen tarih için geçerli kur
     */
    public static function getRateForDate(
        string $fromCurrency, 
        string $toCurrency, 
        Carbon $date
    ): ?float {
        $rate = static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('is_active', true)
            ->where(function($query) use ($date) {
                $query->whereNull('valid_from')
                      ->orWhere('valid_from', '<=', $date);
            })
            ->where(function($query) use ($date) {
                $query->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', $date);
            })
            ->latest('valid_from')
            ->first();

        return $rate ? $rate->rate : null;
    }

    /**
     * Para birimi dönüştür
     */
    public function convert(float $amount): float
    {
        return $amount * $this->rate;
    }
}
