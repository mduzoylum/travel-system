<?php

namespace App\DDD\Modules\Contract\Domain\Services;

use App\DDD\Modules\Contract\Domain\Entities\Contract;
use App\DDD\Modules\Contract\Domain\ValueObjects\Money;
use App\DDD\Modules\Contract\Domain\ValueObjects\PricingPeriod;

class ContractPricingService
{
    public function calculateSalePrice(
        Money $basePrice, 
        float $commissionRate, 
        float $serviceFee = 0
    ): Money {
        $commission = $basePrice->multiply($commissionRate / 100);
        $serviceFeeAmount = new Money($serviceFee, $basePrice->getCurrency());
        
        return $basePrice->add($commission)->add($serviceFeeAmount);
    }

    public function calculateCommission(
        Money $basePrice, 
        Money $salePrice
    ): Money {
        return $salePrice->subtract($basePrice);
    }

    public function calculateCommissionRate(
        Money $basePrice, 
        Money $salePrice
    ): float {
        if ($salePrice->getAmount() === 0) {
            return 0;
        }

        $commission = $this->calculateCommission($basePrice, $salePrice);
        return ($commission->getAmount() / $salePrice->getAmount()) * 100;
    }

    public function isPriceInRange(
        Money $price, 
        Money $minPrice, 
        Money $maxPrice
    ): bool {
        return $price->isGreaterThan($minPrice) && $price->isLessThan($maxPrice);
    }

    public function applySeasonalPricing(
        Money $basePrice, 
        PricingPeriod $period
    ): Money {
        $seasonalMultiplier = $this->getSeasonalMultiplier($period);
        return $basePrice->multiply($seasonalMultiplier);
    }

    private function getSeasonalMultiplier(PricingPeriod $period): float
    {
        // Sezonsal fiyatlandırma kuralları
        return match ($period->getSeason()) {
            'high' => 1.3,    // Yüksek sezon
            'medium' => 1.1,  // Orta sezon
            'low' => 0.9,     // Düşük sezon
            default => 1.0
        };
    }
} 