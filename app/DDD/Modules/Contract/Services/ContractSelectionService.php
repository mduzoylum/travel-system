<?php

namespace App\DDD\Modules\Contract\Services;

use App\DDD\Modules\Contract\Models\Contract;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\Firm\Models\Firm;
use Carbon\Carbon;

/**
 * Kontrat Seçim Servisi
 * 
 * Bu servis, bir otel ve firma için en uygun kontratı seçer.
 * Öncelik sırası:
 * 1. Firmaya özel, aktif ve geçerli tarihli kontrat
 * 2. Genel kontrat (firm_id null), aktif ve geçerli tarihli
 * 
 * Eğer firmaya özel kontratın tarihi dolmuşsa, genel kontrat kullanılır.
 */
class ContractSelectionService
{
    /**
     * Belirli bir otel ve firma için en uygun kontratı seç
     * 
     * @param Hotel|int $hotel
     * @param Firm|int|null $firm
     * @param string|null $date Kontrol edilecek tarih (default: bugün)
     * @return Contract|null
     */
    public function getContractForFirm($hotel, $firm = null, ?string $date = null): ?Contract
    {
        $hotelId = $hotel instanceof Hotel ? $hotel->id : $hotel;
        $firmId = $firm instanceof Firm ? $firm->id : $firm;
        $checkDate = $date ? Carbon::parse($date) : Carbon::now();

        // 1. Önce firmaya özel kontratı kontrol et
        if ($firmId) {
            $firmContract = $this->findActiveContract($hotelId, $firmId, $checkDate);
            if ($firmContract) {
                return $firmContract;
            }
        }

        // 2. Firmaya özel kontrat yoksa veya geçersizse, genel kontratı kontrol et
        $generalContract = $this->findActiveContract($hotelId, null, $checkDate);
        
        return $generalContract;
    }

    /**
     * Birden fazla otel için firma kontratlarını toplu al
     * 
     * @param array $hotelIds
     * @param int|null $firmId
     * @param string|null $date
     * @return array Otel ID'si => Contract mapping
     */
    public function getContractsForHotels(array $hotelIds, ?int $firmId = null, ?string $date = null): array
    {
        $contracts = [];
        foreach ($hotelIds as $hotelId) {
            $contract = $this->getContractForFirm($hotelId, $firmId, $date);
            if ($contract) {
                $contracts[$hotelId] = $contract;
            }
        }
        return $contracts;
    }

    /**
     * Belirli bir otel için tüm aktif kontratları getir (hem genel hem özel)
     * 
     * @param int $hotelId
     * @param string|null $date
     * @return array ['general' => Contract|null, 'firms' => [firmId => Contract]]
     */
    public function getAllContractsForHotel(int $hotelId, ?string $date = null): array
    {
        $checkDate = $date ? Carbon::parse($date) : Carbon::now();
        
        $result = [
            'general' => null,
            'firms' => []
        ];

        // Genel kontrat
        $result['general'] = $this->findActiveContract($hotelId, null, $checkDate);

        // Firmaya özel kontratlar
        $firmContracts = Contract::where('hotel_id', $hotelId)
            ->whereNotNull('firm_id')
            ->where('is_active', true)
            ->where('start_date', '<=', $checkDate)
            ->where('end_date', '>=', $checkDate)
            ->get();

        foreach ($firmContracts as $contract) {
            $result['firms'][$contract->firm_id] = $contract;
        }

        return $result;
    }

    /**
     * Bir kullanıcı için hangi kontratın geçerli olduğunu belirle
     * Kullanıcının firmasına göre kontrat seçer
     * 
     * @param Hotel|int $hotel
     * @param \App\Models\User $user
     * @param string|null $date
     * @return Contract|null
     */
    public function getContractForUser($hotel, $user, ?string $date = null): ?Contract
    {
        // Kullanıcının firmasını bul
        $firmUser = $user->firmUsers()->first();
        $firmId = $firmUser ? $firmUser->firm_id : null;

        return $this->getContractForFirm($hotel, $firmId, $date);
    }

    /**
     * Aktif kontrat bul
     * 
     * @param int $hotelId
     * @param int|null $firmId
     * @param Carbon $date
     * @return Contract|null
     */
    protected function findActiveContract(int $hotelId, ?int $firmId, Carbon $date): ?Contract
    {
        $query = Contract::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);

        if ($firmId === null) {
            $query->whereNull('firm_id');
        } else {
            $query->where('firm_id', $firmId);
        }

        return $query->first();
    }

    /**
     * Bir kontratın belirli bir tarihte geçerli olup olmadığını kontrol et
     * 
     * @param Contract $contract
     * @param string|null $date
     * @return bool
     */
    public function isContractValid(Contract $contract, ?string $date = null): bool
    {
        $checkDate = $date ? Carbon::parse($date) : Carbon::now();
        
        return $contract->is_active 
            && $contract->start_date <= $checkDate 
            && $contract->end_date >= $checkDate;
    }

    /**
     * İki kontrat arasında hangisi öncelikli olduğunu belirle
     * Firmaya özel kontrat her zaman genel kontratın önündedir
     * 
     * @param Contract|null $contract1
     * @param Contract|null $contract2
     * @return Contract|null
     */
    public function prioritizeContracts(?Contract $contract1, ?Contract $contract2): ?Contract
    {
        if (!$contract1) return $contract2;
        if (!$contract2) return $contract1;

        // Firmaya özel kontrat varsa, öncelik ondadır
        if ($contract1->firm_id !== null && $contract2->firm_id === null) {
            return $contract1;
        }
        if ($contract2->firm_id !== null && $contract1->firm_id === null) {
            return $contract2;
        }

        // Her ikisi de aynı tipte ise, daha uzun süreli olanı seç
        return $contract1->end_date->gte($contract2->end_date) ? $contract1 : $contract2;
    }
}

