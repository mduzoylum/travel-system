<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Profit\Models\ProfitCalculation;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Firm\Models\Firm;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class ProfitCalculationSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = Reservation::where('status', 'approved')->get();

        foreach ($reservations as $reservation) {
            // Check if contractRoom, contract, and hotel exist
            if (!$reservation->contractRoom || !$reservation->contractRoom->contract || !$reservation->contractRoom->contract->hotel) {
                continue;
            }
            $contract = $reservation->contractRoom->contract;
            $firm = $contract->firm;
            $supplier = $reservation->contractRoom->contract->hotel->supplier;
            
            $basePrice = $reservation->contractRoom->base_price;
            $salePrice = $reservation->total_price;
            
            // Calculate service fee (5% of sale price)
            $serviceFee = $salePrice * 0.05;
            
            // Calculate commission (10% of base price)
            $commission = $basePrice * 0.10;
            
            // Calculate profit margin (difference between sale and base price)
            $profitMargin = $salePrice - $basePrice;
            
            // Calculate total cost
            $totalCost = $basePrice + $serviceFee + $commission;
            
            // Calculate profit amount
            $profitAmount = $salePrice - $totalCost;
            
            // Calculate profit percentage
            $profitPercentage = $salePrice > 0 ? ($profitAmount / $salePrice) * 100 : 0;
            
            // Determine status based on reservation status
            $status = 'confirmed';
            if ($reservation->status === 'pending') {
                $status = 'draft';
            } elseif ($reservation->status === 'cancelled') {
                $status = 'cancelled';
            }

            ProfitCalculation::firstOrCreate(
                [
                    'reservation_id' => $reservation->id,
                    'contract_id' => $contract->id,
                    'firm_id' => $firm->id,
                ],
                [
                    'supplier_id' => $supplier ? $supplier->id : null,
                    'base_price' => $basePrice,
                    'service_fee' => $serviceFee,
                    'commission' => $commission,
                    'profit_margin' => $profitMargin,
                    'total_cost' => $totalCost,
                    'sale_price' => $salePrice,
                    'profit_amount' => $profitAmount,
                    'profit_percentage' => $profitPercentage,
                    'currency' => 'TRY',
                    'calculation_details' => json_encode([
                        'reservation_duration' => $reservation->checkin_date . ' to ' . $reservation->checkout_date,
                        'guest_count' => $reservation->guest_count,
                        'room_type' => $reservation->contractRoom->room_type ?? null,
                        'meal_plan' => $reservation->contractRoom->meal_plan ?? null,
                        'hotel_name' => $contract->hotel->name ?? null,
                        'hotel_stars' => $contract->hotel->stars ?? null,
                        'calculation_date' => now()->toISOString(),
                    ]),
                    'status' => $status,
                ]
            );
        }

        // Create some sample calculations for contracts without reservations
        $contracts = Contract::all();
        foreach ($contracts as $contract) {
            if (!$contract->hotel) {
                continue;
            }
            if (rand(1, 10) <= 3) { // 30% chance
                $basePrice = $contract->hotel->min_price;
                $salePrice = $basePrice * 1.3; // 30% markup
                
                $serviceFee = $salePrice * 0.05;
                $commission = $basePrice * 0.10;
                $profitMargin = $salePrice - $basePrice;
                $totalCost = $basePrice + $serviceFee + $commission;
                $profitAmount = $salePrice - $totalCost;
                $profitPercentage = ($profitAmount / $salePrice) * 100;

                ProfitCalculation::firstOrCreate(
                    [
                        'reservation_id' => null,
                        'contract_id' => $contract->id,
                        'firm_id' => $contract->firm_id,
                    ],
                    [
                        'supplier_id' => $contract->hotel->supplier_id,
                        'base_price' => $basePrice,
                        'service_fee' => $serviceFee,
                        'commission' => $commission,
                        'profit_margin' => $profitMargin,
                        'total_cost' => $totalCost,
                        'sale_price' => $salePrice,
                        'profit_amount' => $profitAmount,
                        'profit_percentage' => $profitPercentage,
                        'currency' => 'TRY',
                        'calculation_details' => json_encode([
                            'hotel_name' => $contract->hotel->name ?? null,
                            'hotel_stars' => $contract->hotel->stars ?? null,
                            'calculation_type' => 'contract_analysis',
                            'calculation_date' => now()->toISOString(),
                        ]),
                        'status' => 'draft',
                    ]
                );
            }
        }
    }
} 