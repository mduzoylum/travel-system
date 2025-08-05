<?php

namespace App\DDD\Modules\Profit\Models;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Firm\Models\Firm;

class ServiceFee extends Model
{
    protected $fillable = [
        'name', 'description', 'firm_id', 'service_type', 'fee_type',
        'fee_value', 'min_amount', 'max_amount', 'currency', 'is_active', 'is_mandatory'
    ];

    protected $casts = [
        'fee_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean'
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * Servis ücretini hesapla
     */
    public function calculateFee($amount): float
    {
        $fee = 0;

        if ($this->fee_type === 'percentage') {
            $fee = $amount * ($this->fee_value / 100);
        } else {
            $fee = $this->fee_value;
        }

        // Min/Max kontrolü
        if ($this->min_amount && $fee < $this->min_amount) {
            $fee = $this->min_amount;
        }

        if ($this->max_amount && $fee > $this->max_amount) {
            $fee = $this->max_amount;
        }

        return $fee;
    }

    /**
     * Servis tipinin açıklamasını getir
     */
    public function getServiceTypeDescription(): string
    {
        $types = [
            'reservation' => 'Rezervasyon',
            'cancellation' => 'İptal',
            'modification' => 'Değişiklik',
            'booking' => 'Rezervasyon'
        ];

        return $types[$this->service_type] ?? $this->service_type;
    }

    /**
     * Ücret tipinin açıklamasını getir
     */
    public function getFeeTypeDescription(): string
    {
        $types = [
            'percentage' => 'Yüzde',
            'fixed' => 'Sabit'
        ];

        return $types[$this->fee_type] ?? $this->fee_type;
    }

    /**
     * Servis ücreti açıklamasını getir
     */
    public function getDescription(): string
    {
        $feeDesc = $this->fee_type === 'percentage' 
            ? "%{$this->fee_value}" 
            : "{$this->fee_value} {$this->currency}";

        return "{$this->getServiceTypeDescription()} - {$feeDesc}";
    }
}
