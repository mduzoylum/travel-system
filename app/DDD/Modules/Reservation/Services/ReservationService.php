<?php

namespace App\DDD\Modules\Reservation\Services;

use App\Models\User;
use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Contract\Models\ContractRoom;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\DDD\Modules\Contract\Models\RoomAvailability;
use App\DDD\Modules\UserAccessRule\Services\AccessRuleEvaluatorService;
use App\DDD\Modules\Contract\Services\ContractSelectionService;
use App\DDD\Modules\Contract\Services\PricingService;

class ReservationService
{
    protected ContractSelectionService $contractSelectionService;
    protected PricingService $pricingService;

    public function __construct()
    {
        $this->contractSelectionService = app(ContractSelectionService::class);
        $this->pricingService = app(PricingService::class);
    }
    public function makeReservation(User $user, ContractRoom $room, string $checkinDate, string $checkoutDate, int $guestCount = 1): Reservation
    {
        // 1. Kural: kullanıcı bu odaya erişebiliyor mu?
        $canAccess = app(AccessRuleEvaluatorService::class)->canUserAccessHotel($user, $room->contract->hotel);

        if (!$canAccess) {
            throw new \Exception("Bu odaya erişim izniniz yok.");
        }

        // 2. Kural: stok var mı? (yalnızca checkin günü kontrol ediliyor burada basitçe)
        $availability = RoomAvailability::where('contract_room_id', $room->id)
            ->where('date', $checkinDate)
            ->first();

        if (!$availability || $availability->stock <= 0) {
            throw new \Exception("Seçilen tarihte oda kalmamış.");
        }

        // 3. Kural: kredi kontrolü yapılmalı (henüz implement edilmedi)

        // 4. Toplam fiyat hesapla (servis bedeli dahil)
        $priceCalculation = $this->pricingService->calculatePriceForUser($room, $user, $guestCount);
        $total = $priceCalculation['total_price'];

        // 5. Rezervasyon oluştur
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'contract_room_id' => $room->id,
            'checkin_date' => $checkinDate,
            'checkout_date' => $checkoutDate,
            'guest_count' => $guestCount,
            'total_price' => $total,
            'status' => $this->shouldRequireApproval($user, $room) ? 'awaiting_approval' : 'approved',
        ]);

        // 6. Stok azalt (şimdilik sadece checkin günü için)
        $availability->decrement('stock');

        // 7. Onaya gitmesi gerekiyorsa mail kuyruğuna eklenecek (bir sonraki aşama)

        return $reservation;
    }

    protected function shouldRequireApproval(User $user, ContractRoom $room): bool
    {
        // Örnek kural: 4+ yıldızlı oteller sadece yöneticilere serbest
        $hotel = $room->contract->hotel;

        if ($hotel->stars >= 4 && $user->firmUser?->role !== 'ceo') {
            return true;
        }

        return false;
    }

    /**
     * Kullanıcı için bir oteldeki müsait odaları getir
     * Kullanıcının firmasına göre uygun kontratı seçer (firmaya özel > genel)
     * 
     * @param User $user
     * @param Hotel|int $hotel
     * @param string $checkinDate
     * @param string $checkoutDate
     * @return array
     */
    public function getAvailableRoomsForUser(User $user, $hotel, string $checkinDate, string $checkoutDate): array
    {
        // Kullanıcı için uygun kontratı seç
        $contract = $this->contractSelectionService->getContractForUser($hotel, $user, $checkinDate);

        if (!$contract) {
            return [];
        }

        // Erişim kontrolü
        $canAccess = app(AccessRuleEvaluatorService::class)->canUserAccessHotel($user, $contract->hotel);
        if (!$canAccess) {
            return [];
        }

        // Kontratın odalarını getir
        $rooms = $contract->rooms()
            ->with(['availabilities' => function($query) use ($checkinDate) {
                $query->where('date', $checkinDate)->where('stock', '>', 0);
            }])
            ->get();

        return [
            'contract' => $contract,
            'rooms' => $rooms,
            'hotel' => $contract->hotel,
            'is_firm_specific' => $contract->firm_id !== null
        ];
    }

    /**
     * Kullanıcı için birden fazla oteldeki müsait odaları getir
     * 
     * @param User $user
     * @param array $hotelIds
     * @param string $checkinDate
     * @param string $checkoutDate
     * @return array
     */
    public function searchHotelsForUser(User $user, array $hotelIds, string $checkinDate, string $checkoutDate): array
    {
        $results = [];
        
        foreach ($hotelIds as $hotelId) {
            $hotelData = $this->getAvailableRoomsForUser($user, $hotelId, $checkinDate, $checkoutDate);
            if (!empty($hotelData)) {
                $results[$hotelId] = $hotelData;
            }
        }

        return $results;
    }

    /**
     * Bir firma için hangi otellerde özel kontrat olduğunu göster
     * 
     * @param int $firmId
     * @param string|null $date
     * @return array
     */
    public function getFirmSpecificHotels(int $firmId, ?string $date = null): array
    {
        $contracts = Contract::whereNotNull('firm_id')
            ->where('firm_id', $firmId)
            ->where('is_active', true)
            ->when($date, function($query, $date) {
                $query->where('start_date', '<=', $date)
                      ->where('end_date', '>=', $date);
            })
            ->whereHas('hotel.supplier', function($q) {
                $q->where('is_active', true);
            })
            ->with('hotel')
            ->get();

        return $contracts->pluck('hotel')->unique('id')->all();
    }

    /**
     * Admin için: Bir otel için tüm kontratları ve öncelik durumunu göster
     * 
     * @param int $hotelId
     * @param string|null $date
     * @return array
     */
    public function getContractPriorityStatus(int $hotelId, ?string $date = null): array
    {
        return $this->contractSelectionService->getAllContractsForHotel($hotelId, $date);
    }
}
