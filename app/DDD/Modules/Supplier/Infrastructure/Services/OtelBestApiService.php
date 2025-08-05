<?php

namespace App\DDD\Modules\Supplier\Infrastructure\Services;

use App\DDD\Modules\Supplier\Domain\Entities\Supplier;
use App\DDD\Modules\Supplier\Domain\ValueObjects\ApiCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtelBestApiService
{
    private string $baseUrl;
    private ApiCredentials $credentials;

    public function __construct(Supplier $supplier)
    {
        $this->baseUrl = $supplier->api_endpoint;
        $this->credentials = $supplier->getApiCredentials();
    }

    public function fetchHotels(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->credentials->getApiKey(),
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/hotels');

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('OtelBest API Error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('OtelBest API Exception: ' . $e->getMessage());
            return [];
        }
    }

    public function fetchRoomAvailability(int $hotelId, string $checkIn, string $checkOut): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->credentials->getApiKey(),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/availability', [
                'hotel_id' => $hotelId,
                'check_in' => $checkIn,
                'check_out' => $checkOut
            ]);

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('OtelBest Availability API Error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('OtelBest Availability API Exception: ' . $e->getMessage());
            return [];
        }
    }

    public function createReservation(array $reservationData): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->credentials->getApiKey(),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/reservations', $reservationData);

            if ($response->successful()) {
                return $response->json('data');
            }

            Log::error('OtelBest Reservation API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('OtelBest Reservation API Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function cancelReservation(string $reservationId): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->credentials->getApiKey(),
                'Content-Type' => 'application/json'
            ])->delete($this->baseUrl . '/reservations/' . $reservationId);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('OtelBest Cancel API Exception: ' . $e->getMessage());
            return false;
        }
    }
} 