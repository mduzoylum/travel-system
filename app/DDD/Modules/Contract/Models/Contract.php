<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'hotel_id', 'firm_id', 'start_date', 'end_date', 'currency', 'is_active',
        'base_price', 'commission_rate', 'service_fee', 'description', 'auto_renewal', 'payment_terms'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'auto_renewal' => 'boolean',
        'base_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'service_fee' => 'decimal:2'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function rooms()
    {
        return $this->hasMany(ContractRoom::class);
    }

    public function firm()
    {
        return $this->belongsTo(\App\DDD\Modules\Firm\Models\Firm::class);
    }

    /**
     * Kontratın süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Kontratın süresi yakında dolacak mı? (30 gün içinde)
     */
    public function isExpiringSoon(): bool
    {
        return $this->end_date->diffInDays(now()) <= 30 && !$this->isExpired();
    }

    /**
     * Kontratın toplam değeri
     */
    public function getTotalValue(): float
    {
        return $this->rooms->sum('sale_price');
    }

    /**
     * Kontratın toplam komisyonu
     */
    public function getTotalCommission(): float
    {
        return $this->getTotalValue() * ($this->commission_rate / 100);
    }

    /**
     * Kontratın kalan gün sayısı
     */
    public function getRemainingDays(): int
    {
        return max(0, $this->end_date->diffInDays(now()));
    }

    /**
     * Kontratın durumu
     */
    public function getStatus(): string
    {
        if ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isExpiringSoon()) {
            return 'expiring_soon';
        } else {
            return 'active';
        }
    }

    /**
     * Kontratın durum badge'i
     */
    public function getStatusBadge(): string
    {
        switch ($this->getStatus()) {
            case 'expired':
                return 'bg-danger';
            case 'expiring_soon':
                return 'bg-warning';
            default:
                return 'bg-success';
        }
    }
}
