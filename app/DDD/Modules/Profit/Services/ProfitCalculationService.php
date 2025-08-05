<?php

namespace App\DDD\Modules\Profit\Services;

use App\DDD\Modules\Profit\Models\ProfitRule;
use App\DDD\Modules\Profit\Models\ServiceFee;
use App\DDD\Modules\Profit\Models\ProfitCalculation;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class ProfitCalculationService
{
    /**
     * Kar hesaplaması yap
     */
    public function calculateProfit(array $data): ProfitCalculation
    {
        $calculation = new ProfitCalculation();
        $calculation->firm_id = $data['firm_id'];
        $calculation->supplier_id = $data['supplier_id'] ?? null;
        $calculation->contract_id = $data['contract_id'] ?? null;
        $calculation->reservation_id = $data['reservation_id'] ?? null;
        $calculation->base_price = $data['base_price'];
        $calculation->currency = $data['currency'] ?? 'TRY';
        $calculation->status = 'draft';

        // Servis ücretini hesapla
        $calculation->service_fee = $this->calculateServiceFee($data);

        // Komisyonu hesapla
        $calculation->commission = $this->calculateCommission($data);

        // Kar marjını hesapla
        $calculation->profit_margin = $this->calculateProfitMargin($data);

        // Toplam kar hesaplaması
        $calculation->calculateProfit();

        // Hesaplama detaylarını kaydet
        $calculation->calculation_details = [
            'service_fee_rule' => $this->getAppliedServiceFeeRule($data),
            'commission_rule' => $this->getAppliedCommissionRule($data),
            'profit_margin_rule' => $this->getAppliedProfitMarginRule($data),
            'calculation_date' => now()->toISOString()
        ];

        return $calculation;
    }

    /**
     * Servis ücretini hesapla
     */
    private function calculateServiceFee(array $data): float
    {
        $serviceFees = ServiceFee::where('is_active', true)
            ->where(function ($query) use ($data) {
                $query->where('firm_id', $data['firm_id'])
                      ->orWhereNull('firm_id');
            })
            ->where('service_type', $data['service_type'] ?? 'reservation')
            ->orderBy('priority', 'desc')
            ->get();

        $totalFee = 0;
        foreach ($serviceFees as $serviceFee) {
            $totalFee += $serviceFee->calculateFee($data['base_price']);
        }

        return $totalFee;
    }

    /**
     * Komisyonu hesapla
     */
    private function calculateCommission(array $data): float
    {
        $profitRules = ProfitRule::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $commission = 0;
        foreach ($profitRules as $rule) {
            if ($rule->isApplicable($data)) {
                $commission = $rule->calculateFee($data['base_price']);
                break; // İlk uygun kuralı kullan
            }
        }

        return $commission;
    }

    /**
     * Kar marjını hesapla
     */
    private function calculateProfitMargin(array $data): float
    {
        // Basit kar marjı hesaplama (varsayılan %10)
        $defaultMargin = $data['base_price'] * 0.10;

        // Firma bazlı özel kar marjı kontrolü
        $firm = Firm::find($data['firm_id']);
        if ($firm && $firm->profit_margin) {
            return $firm->profit_margin;
        }

        return $defaultMargin;
    }

    /**
     * Uygulanan servis ücreti kuralını getir
     */
    private function getAppliedServiceFeeRule(array $data): ?array
    {
        $serviceFee = ServiceFee::where('is_active', true)
            ->where(function ($query) use ($data) {
                $query->where('firm_id', $data['firm_id'])
                      ->orWhereNull('firm_id');
            })
            ->where('service_type', $data['service_type'] ?? 'reservation')
            ->first();

        if (!$serviceFee) {
            return null;
        }

        return [
            'id' => $serviceFee->id,
            'name' => $serviceFee->name,
            'fee_type' => $serviceFee->fee_type,
            'fee_value' => $serviceFee->fee_value,
            'currency' => $serviceFee->currency
        ];
    }

    /**
     * Uygulanan komisyon kuralını getir
     */
    private function getAppliedCommissionRule(array $data): ?array
    {
        $profitRule = ProfitRule::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->first(function ($rule) use ($data) {
                return $rule->isApplicable($data);
            });

        if (!$profitRule) {
            return null;
        }

        return [
            'id' => $profitRule->id,
            'name' => $profitRule->name,
            'fee_type' => $profitRule->fee_type,
            'fee_value' => $profitRule->fee_value,
            'description' => $profitRule->getDescription()
        ];
    }

    /**
     * Uygulanan kar marjı kuralını getir
     */
    private function getAppliedProfitMarginRule(array $data): ?array
    {
        $firm = Firm::find($data['firm_id']);
        
        return [
            'firm_id' => $firm->id,
            'firm_name' => $firm->name,
            'profit_margin' => $firm->profit_margin ?? ($data['base_price'] * 0.10),
            'is_custom' => !is_null($firm->profit_margin)
        ];
    }

    /**
     * Firma için kar raporu oluştur
     */
    public function generateProfitReport(int $firmId, string $startDate, string $endDate): array
    {
        $calculations = ProfitCalculation::where('firm_id', $firmId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'confirmed')
            ->get();

        $totalRevenue = $calculations->sum('sale_price');
        $totalCost = $calculations->sum('total_cost');
        $totalProfit = $calculations->sum('profit_amount');
        $averageProfitPercentage = $calculations->avg('profit_percentage');

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'summary' => [
                'total_transactions' => $calculations->count(),
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'average_profit_percentage' => $averageProfitPercentage
            ],
            'breakdown' => [
                'by_supplier' => $this->getBreakdownBySupplier($calculations),
                'by_month' => $this->getBreakdownByMonth($calculations),
                'profit_distribution' => $this->getProfitDistribution($calculations)
            ]
        ];
    }

    /**
     * Tedarikçi bazında dağılım
     */
    private function getBreakdownBySupplier($calculations): array
    {
        return $calculations->groupBy('supplier_id')
            ->map(function ($group) {
                return [
                    'supplier_name' => $group->first()->supplier?->name ?? 'Bilinmeyen',
                    'count' => $group->count(),
                    'total_profit' => $group->sum('profit_amount'),
                    'average_profit' => $group->avg('profit_amount')
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Aylık dağılım
     */
    private function getBreakdownByMonth($calculations): array
    {
        return $calculations->groupBy(function ($calculation) {
            return $calculation->created_at->format('Y-m');
        })
            ->map(function ($group) {
                return [
                    'month' => $group->first()->created_at->format('Y-m'),
                    'count' => $group->count(),
                    'total_profit' => $group->sum('profit_amount'),
                    'total_revenue' => $group->sum('sale_price')
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Kar dağılımı
     */
    private function getProfitDistribution($calculations): array
    {
        $excellent = $calculations->where('profit_percentage', '>=', 20)->count();
        $good = $calculations->whereBetween('profit_percentage', [10, 19.99])->count();
        $average = $calculations->whereBetween('profit_percentage', [5, 9.99])->count();
        $low = $calculations->where('profit_percentage', '<', 5)->count();

        return [
            'excellent' => $excellent,
            'good' => $good,
            'average' => $average,
            'low' => $low
        ];
    }
} 