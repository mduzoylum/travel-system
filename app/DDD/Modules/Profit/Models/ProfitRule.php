<?php

namespace App\DDD\Modules\Profit\Models;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class ProfitRule extends Model
{
    protected $fillable = [
        'name', 'description', 'firm_id', 'supplier_id', 'destination',
        'trip_type', 'travel_type', 'fee_type', 'fee_value', 'min_fee', 'max_fee',
        'tier_rules', 'is_active', 'priority'
    ];

    protected $casts = [
        'fee_value' => 'decimal:2',
        'min_fee' => 'decimal:2',
        'max_fee' => 'decimal:2',
        'tier_rules' => 'array',
        'is_active' => 'boolean'
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Kuralın geçerli olup olmadığını kontrol et
     */
    public function isApplicable($data): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Firma kontrolü
        if ($this->firm_id && $data['firm_id'] != $this->firm_id) {
            return false;
        }

        // Tedarikçi kontrolü
        if ($this->supplier_id && isset($data['supplier_id']) && $data['supplier_id'] != $this->supplier_id) {
            return false;
        }

        // Destinasyon kontrolü
        if ($this->destination && $data['destination'] != $this->destination) {
            return false;
        }

        // Seyahat tipi kontrolü
        if ($data['trip_type'] != $this->trip_type) {
            return false;
        }

        // Yolculuk tipi kontrolü
        if ($data['travel_type'] != $this->travel_type) {
            return false;
        }

        return true;
    }

    /**
     * Komisyon tutarını hesapla
     */
    public function calculateFee($basePrice): float
    {
        $fee = 0;

        switch ($this->fee_type) {
            case 'percentage':
                $fee = $basePrice * ($this->fee_value / 100);
                break;
            
            case 'fixed':
                $fee = $this->fee_value;
                break;
            
            case 'tiered':
                $fee = $this->calculateTieredFee($basePrice);
                break;
        }

        // Min/Max kontrolü
        if ($this->min_fee && $fee < $this->min_fee) {
            $fee = $this->min_fee;
        }

        if ($this->max_fee && $fee > $this->max_fee) {
            $fee = $this->max_fee;
        }

        return $fee;
    }

    /**
     * Katmanlı fiyatlandırma hesaplama
     */
    private function calculateTieredFee($basePrice): float
    {
        if (!$this->tier_rules) {
            return 0;
        }

        $fee = 0;
        foreach ($this->tier_rules as $tier) {
            if ($basePrice >= $tier['min'] && $basePrice <= $tier['max']) {
                if ($tier['type'] === 'percentage') {
                    $fee = $basePrice * ($tier['value'] / 100);
                } else {
                    $fee = $tier['value'];
                }
                break;
            }
        }

        return $fee;
    }

    /**
     * Kural açıklamasını getir
     */
    public function getDescription(): string
    {
        $parts = [];

        if ($this->firm) {
            $parts[] = "Firma: {$this->firm->name}";
        }

        if ($this->supplier) {
            $parts[] = "Tedarikçi: {$this->supplier->name}";
        }

        if ($this->destination) {
            $parts[] = "Destinasyon: {$this->destination}";
        }

        $parts[] = "Tip: " . ucfirst($this->trip_type);
        $parts[] = "Yolculuk: " . ($this->travel_type === 'one_way' ? 'Tek Yön' : 'Gidiş-Dönüş');

        $feeDesc = match($this->fee_type) {
            'percentage' => "%{$this->fee_value}",
            'fixed' => "₺{$this->fee_value}",
            'tiered' => "Katmanlı",
            default => $this->fee_value
        };

        $parts[] = "Ücret: {$feeDesc}";

        return implode(' | ', $parts);
    }
}
