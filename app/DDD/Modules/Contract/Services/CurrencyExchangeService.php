<?php

namespace App\DDD\Modules\Contract\Services;

use App\DDD\Modules\Contract\Models\ExchangeRate;
use Carbon\Carbon;

/**
 * Para Birimi Dönüştürme Servisi
 * 
 * Bu servis, farklı para birimleri arasında dönüştürme işlemlerini yönetir.
 */
class CurrencyExchangeService
{
    /**
     * Para birimini dönüştür
     * 
     * @param float $amount Dönüştürülecek miktar
     * @param string $fromCurrency Kaynak para birimi
     * @param string $toCurrency Hedef para birimi
     * @param Carbon|null $date Tarih (opsiyonel, null ise bugünün kuru kullanılır)
     * @return float Dönüştürülmüş miktar
     * @throws \Exception Kur bulunamazsa
     */
    public function convert(
        float $amount, 
        string $fromCurrency, 
        string $toCurrency, 
        ?Carbon $date = null
    ): float {
        // Aynı para birimi ise dönüştürmeye gerek yok
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $date = $date ?? Carbon::now();
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency, $date);

        if ($rate === null) {
            throw new \Exception(
                "Döviz kuru bulunamadı: {$fromCurrency} -> {$toCurrency} (Tarih: {$date->format('Y-m-d')})"
            );
        }

        return $amount * $rate;
    }

    /**
     * Döviz kurunu al
     * 
     * @param string $fromCurrency Kaynak para birimi
     * @param string $toCurrency Hedef para birimi
     * @param Carbon $date Tarih
     * @return float|null
     */
    public function getExchangeRate(
        string $fromCurrency, 
        string $toCurrency, 
        Carbon $date
    ): ?float {
        return ExchangeRate::getRateForDate($fromCurrency, $toCurrency, $date);
    }

    /**
     * Birden fazla miktarı aynı anda dönüştür
     * 
     * @param array $amounts ['amount' => float, 'currency' => string]
     * @param string $targetCurrency Hedef para birimi
     * @param Carbon|null $date Tarih
     * @return array
     */
    public function convertMultiple(
        array $amounts, 
        string $targetCurrency, 
        ?Carbon $date = null
    ): array {
        $converted = [];
        
        foreach ($amounts as $item) {
            $converted[] = [
                'amount' => $this->convert(
                    $item['amount'], 
                    $item['currency'], 
                    $targetCurrency, 
                    $date
                ),
                'currency' => $targetCurrency,
                'original_amount' => $item['amount'],
                'original_currency' => $item['currency']
            ];
        }

        return $converted;
    }

    /**
     * Toplam tutarı hesapla (farklı para birimlerinden)
     * 
     * @param array $amounts ['amount' => float, 'currency' => string]
     * @param string $targetCurrency Hedef para birimi
     * @param Carbon|null $date Tarih
     * @return float
     */
    public function sumMultiple(
        array $amounts, 
        string $targetCurrency, 
        ?Carbon $date = null
    ): float {
        $total = 0;
        
        foreach ($amounts as $item) {
            $converted = $this->convert(
                $item['amount'], 
                $item['currency'], 
                $targetCurrency, 
                $date
            );
            $total += $converted;
        }

        return $total;
    }
}
