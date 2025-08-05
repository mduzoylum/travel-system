<?php

namespace App\DDD\Modules\Profit\Models;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Reservation\Models\Reservation;

class ProfitCalculation extends Model
{
    protected $fillable = [
        'reservation_id', 'contract_id', 'firm_id', 'supplier_id',
        'base_price', 'service_fee', 'commission', 'profit_margin',
        'total_cost', 'sale_price', 'profit_amount', 'profit_percentage',
        'currency', 'calculation_details', 'status'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'commission' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'calculation_details' => 'array'
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Kar hesaplaması yap
     */
    public function calculateProfit(): void
    {
        $this->total_cost = $this->base_price + $this->service_fee + $this->commission;
        $this->sale_price = $this->total_cost + $this->profit_margin;
        $this->profit_amount = $this->sale_price - $this->total_cost;
        
        if ($this->total_cost > 0) {
            $this->profit_percentage = ($this->profit_amount / $this->total_cost) * 100;
        } else {
            $this->profit_percentage = 0;
        }
    }

    /**
     * Durum açıklamasını getir
     */
    public function getStatusDescription(): string
    {
        $statuses = [
            'draft' => 'Taslak',
            'confirmed' => 'Onaylandı',
            'cancelled' => 'İptal Edildi'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Durum badge rengini getir
     */
    public function getStatusBadge(): string
    {
        switch ($this->status) {
            case 'draft':
                return 'bg-secondary';
            case 'confirmed':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Kar marjı durumunu getir
     */
    public function getProfitStatus(): string
    {
        if ($this->profit_percentage >= 20) {
            return 'excellent';
        } elseif ($this->profit_percentage >= 10) {
            return 'good';
        } elseif ($this->profit_percentage >= 5) {
            return 'average';
        } else {
            return 'low';
        }
    }

    /**
     * Kar marjı badge rengini getir
     */
    public function getProfitBadge(): string
    {
        switch ($this->getProfitStatus()) {
            case 'excellent':
                return 'bg-success';
            case 'good':
                return 'bg-info';
            case 'average':
                return 'bg-warning';
            case 'low':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
}
