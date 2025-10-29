<?php

namespace App\DDD\Modules\Contract\Services;

use App\Models\User;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Contract\Models\ContractRoom;
use App\DDD\Modules\Firm\Models\FirmUser;

/**
 * Fiyat Hesaplama Servisi
 * 
 * Bu servis, kullanıcının firmasına ve şubesine göre 
 * servis bedeli dahil toplam fiyatı hesaplar.
 */
class PricingService
{
    /**
     * Kullanıcı için toplam fiyat hesapla
     * 
     * @param ContractRoom $room
     * @param User $user
     * @param int $guestCount
     * @return array ['base_price', 'service_fee', 'total_price', 'breakdown']
     */
    public function calculatePriceForUser(ContractRoom $room, User $user, int $guestCount = 1): array
    {
        $basePrice = $room->sale_price * $guestCount;
        $serviceFee = $this->getServiceFeeForUser($user);
        $totalPrice = $basePrice + $serviceFee;

        return [
            'base_price' => $basePrice,
            'service_fee' => $serviceFee,
            'total_price' => $totalPrice,
            'breakdown' => [
                'room_price_per_person' => $room->sale_price,
                'guest_count' => $guestCount,
                'service_fee_per_person' => $serviceFee / $guestCount,
                'service_fee_source' => $this->getServiceFeeSource($user)
            ]
        ];
    }

    /**
     * Kullanıcının servis bedelini al
     * 
     * Öncelik sırası:
     * 1. Kullanıcının şube servis bedeli (varsa)
     * 2. Kullanıcının firma servis bedeli
     * 3. Varsayılan servis bedeli (0)
     * 
     * @param User $user
     * @return float
     */
    public function getServiceFeeForUser(User $user): float
    {
        $firmUser = $user->firmUser;
        
        if (!$firmUser) {
            return 0;
        }

        // 1. Önce şube servis bedelini kontrol et
        if ($firmUser->service_fee !== null) {
            return $firmUser->service_fee;
        }

        // 2. Firma servis bedelini kullan
        return $firmUser->firm->service_fee ?? 0;
    }

    /**
     * Servis bedelinin kaynağını al (debug için)
     * 
     * @param User $user
     * @return string
     */
    public function getServiceFeeSource(User $user): string
    {
        $firmUser = $user->firmUser;
        
        if (!$firmUser) {
            return 'Kullanıcı firmaya bağlı değil';
        }

        if ($firmUser->service_fee !== null) {
            return "Şube servis bedeli: {$firmUser->service_fee}";
        }

        $firmServiceFee = $firmUser->firm->service_fee ?? 0;
        return "Firma servis bedeli: {$firmServiceFee}";
    }

    /**
     * Bir firma için ortalama servis bedelini hesapla
     * 
     * @param int $firmId
     * @return array ['firm_service_fee', 'branch_average', 'branch_count', 'details']
     */
    public function getFirmServiceFeeAnalysis(int $firmId): array
    {
        $firm = \App\DDD\Modules\Firm\Models\Firm::find($firmId);
        
        if (!$firm) {
            return [
                'firm_service_fee' => 0,
                'branch_average' => 0,
                'branch_count' => 0,
                'details' => []
            ];
        }

        $branches = $firm->firmUsers()
            ->whereNotNull('service_fee')
            ->get();

        $branchServiceFees = $branches->pluck('service_fee')->toArray();
        $branchAverage = count($branchServiceFees) > 0 ? array_sum($branchServiceFees) / count($branchServiceFees) : 0;

        $details = $branches->map(function($branch) {
            return [
                'user_id' => $branch->user_id,
                'department' => $branch->department,
                'service_fee' => $branch->service_fee
            ];
        })->toArray();

        return [
            'firm_service_fee' => $firm->service_fee,
            'branch_average' => $branchAverage,
            'branch_count' => count($branchServiceFees),
            'details' => $details
        ];
    }

    /**
     * Rezervasyon için fiyat hesapla (gece sayısı dahil)
     * 
     * @param ContractRoom $room
     * @param User $user
     * @param string $checkinDate
     * @param string $checkoutDate
     * @param int $guestCount
     * @return array
     */
    public function calculateReservationPrice(
        ContractRoom $room, 
        User $user, 
        string $checkinDate, 
        string $checkoutDate, 
        int $guestCount = 1
    ): array {
        $checkin = \Carbon\Carbon::parse($checkinDate);
        $checkout = \Carbon\Carbon::parse($checkoutDate);
        $nights = $checkin->diffInDays($checkout);

        $pricePerNight = $this->calculatePriceForUser($room, $user, $guestCount);
        
        return [
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'total_room_price' => $pricePerNight['total_price'] * $nights,
            'total_service_fee' => $pricePerNight['service_fee'] * $nights,
            'grand_total' => $pricePerNight['total_price'] * $nights,
            'breakdown' => [
                'room_price_per_night' => $pricePerNight['base_price'],
                'service_fee_per_night' => $pricePerNight['service_fee'],
                'total_per_night' => $pricePerNight['total_price'],
                'nights' => $nights,
                'service_fee_source' => $pricePerNight['breakdown']['service_fee_source']
            ]
        ];
    }

    /**
     * Çoklu periyot fiyat hesaplama (farklı para birimleri destekli)
     * 
     * @param ContractRoom $room
     * @param User $user
     * @param string $checkinDate
     * @param string $checkoutDate
     * @param string $targetCurrency Hedef para birimi (ör: TRY)
     * @param int $guestCount
     * @return array
     */
    public function calculateMultiPeriodPrice(
        ContractRoom $room, 
        User $user, 
        string $checkinDate, 
        string $checkoutDate, 
        string $targetCurrency = 'TRY',
        int $guestCount = 1
    ): array {
        $checkin = \Carbon\Carbon::parse($checkinDate);
        $checkout = \Carbon\Carbon::parse($checkoutDate);
        
        // Periyotları al
        $periods = $room->getPeriodsForDateRange($checkin, $checkout);
        
        if ($periods->isEmpty()) {
            // Periyot yoksa eski metodu kullan
            return $this->calculateReservationPrice($room, $user, $checkinDate, $checkoutDate, $guestCount);
        }

        $exchangeService = new CurrencyExchangeService();
        $nightlyPrices = [];
        $totalBasePrice = 0;
        $totalSalePrice = 0;

        // Her gün için fiyat hesapla
        $currentDate = $checkin->copy();
        while ($currentDate->lt($checkout)) {
            $period = $room->getPeriodForDate($currentDate);
            
            if ($period) {
                $basePrice = $period->base_price * $guestCount;
                $salePrice = $period->sale_price * $guestCount;

                // Para birimi farklıysa dönüştür
                if ($period->currency !== $targetCurrency) {
                    $basePrice = $exchangeService->convert(
                        $basePrice, 
                        $period->currency, 
                        $targetCurrency, 
                        $currentDate
                    );
                    $salePrice = $exchangeService->convert(
                        $salePrice, 
                        $period->currency, 
                        $targetCurrency, 
                        $currentDate
                    );
                }

                $totalBasePrice += $basePrice;
                $totalSalePrice += $salePrice;

                $nightlyPrices[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'base_price' => $basePrice,
                    'sale_price' => $salePrice,
                    'currency' => $targetCurrency,
                    'period_currency' => $period->currency
                ];
            } else {
                // Periyot bulunamadı - varsayılan fiyat kullan
                $defaultPrice = $this->calculatePriceForUser($room, $user, $guestCount);
                $totalBasePrice += $defaultPrice['base_price'];
                $totalSalePrice += $defaultPrice['total_price'];
            }

            $currentDate->addDay();
        }

        $serviceFee = $this->getServiceFeeForUser($user);
        $nights = count($nightlyPrices);
        $totalServiceFee = $serviceFee * $nights;
        
        return [
            'nights' => $nights,
            'base_price' => $totalBasePrice,
            'sale_price' => $totalSalePrice,
            'service_fee' => $serviceFee,
            'total_service_fee' => $totalServiceFee,
            'grand_total' => $totalSalePrice + $totalServiceFee,
            'currency' => $targetCurrency,
            'nightly_breakdown' => $nightlyPrices,
            'breakdown' => [
                'price_per_night' => $nights > 0 ? $totalSalePrice / $nights : 0,
                'service_fee_per_night' => $serviceFee,
                'nights' => $nights,
                'service_fee_source' => $this->getServiceFeeSource($user)
            ]
        ];
    }
}
