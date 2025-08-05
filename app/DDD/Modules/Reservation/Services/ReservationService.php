<?php

namespace App\DDD\Modules\Reservation\Services;

use App\Models\User;
use App\DDD\Modules\Contract\Models\ContractRoom;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\DDD\Modules\Contract\Models\RoomAvailability;
use App\DDD\Modules\UserAccessRule\Services\AccessRuleEvaluatorService;

class ReservationService
{
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

        // 4. Toplam fiyat hesapla (örnek: tek gecelik sabit fiyat)
        $total = $room->sale_price * $guestCount;

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
}
