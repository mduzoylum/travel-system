<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'hotel_id', 'firm_id', 'start_date', 'end_date', 'currency', 'is_active',
        'base_price', 'commission_rate', 'description', 'auto_renewal', 'payment_terms'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'auto_renewal' => 'boolean',
        'base_price' => 'decimal:2',
        'commission_rate' => 'decimal:2'
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

    /**
     * Bu kontrat genel kontrat mı? (tüm firmalara geçerli)
     */
    public function isGeneralContract(): bool
    {
        return $this->firm_id === null;
    }

    /**
     * Bu kontrat firmaya özel mi?
     */
    public function isFirmSpecific(): bool
    {
        return $this->firm_id !== null;
    }

    /**
     * Kontrat tipini string olarak döndür
     */
    public function getContractType(): string
    {
        return $this->isGeneralContract() ? 'Genel Kontrat' : 'Firmaya Özel';
    }

    /**
     * Kontrat adını döndür (görüntüleme için)
     */
    public function getDisplayName(): string
    {
        if ($this->isGeneralContract()) {
            return '🌍 Genel Kontrat - ' . $this->hotel->name;
        }
        
        return $this->firm->name . ' - ' . $this->hotel->name;
    }

    /**
     * Scope: Genel kontratları getir
     */
    public function scopeGeneral($query)
    {
        return $query->whereNull('firm_id');
    }

    /**
     * Scope: Firmaya özel kontratları getir
     */
    public function scopeFirmSpecific($query, ?int $firmId = null)
    {
        $query = $query->whereNotNull('firm_id');
        
        if ($firmId !== null) {
            $query->where('firm_id', $firmId);
        }
        
        return $query;
    }

    /**
     * Scope: Aktif ve geçerli kontratları getir
     */
    public function scopeActiveAndValid($query, ?string $date = null)
    {
        $checkDate = $date ?? now();
        
        return $query->where('is_active', true)
            ->where('start_date', '<=', $checkDate)
            ->where('end_date', '>=', $checkDate);
    }
}
