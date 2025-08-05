<?php

namespace App\DDD\Modules\Supplier\Infrastructure\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MockApiService
{
    private $baseUrl;
    private $credentials;

    public function __construct(string $baseUrl, array $credentials = [])
    {
        $this->baseUrl = $baseUrl;
        $this->credentials = $credentials;
    }

    /**
     * API bağlantısını test eder
     */
    public function testConnection(): array
    {
        try {
            // Mock response - gerçek API'de bu endpoint çağrılır
            $response = [
                'success' => true,
                'message' => 'Bağlantı başarılı',
                'data' => [
                    'api_version' => 'v1.0',
                    'server_time' => now()->toISOString(),
                    'status' => 'active'
                ]
            ];

            // Simüle edilmiş gecikme
            usleep(500000); // 0.5 saniye

            return $response;
        } catch (\Exception $e) {
            Log::error('Mock API connection test failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Otel listesini çeker
     */
    public function getHotels(array $params = []): array
    {
        try {
            // Mock hotel data
            $mockHotels = [
                [
                    'id' => 'HTL001',
                    'name' => 'Grand Hotel Istanbul',
                    'city' => 'Istanbul',
                    'country' => 'Turkey',
                    'stars' => 5,
                    'address' => 'Taksim Meydanı No:1',
                    'description' => 'Lüks 5 yıldızlı otel',
                    'min_price' => 150.00,
                    'currency' => 'TRY',
                    'amenities' => ['WiFi', 'Pool', 'Spa', 'Restaurant'],
                    'images' => ['hotel1.jpg', 'hotel2.jpg']
                ],
                [
                    'id' => 'HTL002',
                    'name' => 'Blue Sea Resort',
                    'city' => 'Antalya',
                    'country' => 'Turkey',
                    'stars' => 4,
                    'address' => 'Lara Caddesi No:15',
                    'description' => 'Deniz manzaralı resort',
                    'min_price' => 120.00,
                    'currency' => 'TRY',
                    'amenities' => ['WiFi', 'Beach', 'Restaurant'],
                    'images' => ['resort1.jpg']
                ],
                [
                    'id' => 'HTL003',
                    'name' => 'Mountain View Hotel',
                    'city' => 'Bursa',
                    'country' => 'Turkey',
                    'stars' => 3,
                    'address' => 'Uludağ Yolu No:8',
                    'description' => 'Dağ manzaralı otel',
                    'min_price' => 80.00,
                    'currency' => 'TRY',
                    'amenities' => ['WiFi', 'Skiing'],
                    'images' => ['mountain1.jpg']
                ],
                [
                    'id' => 'HTL004',
                    'name' => 'Business Center Hotel',
                    'city' => 'Ankara',
                    'country' => 'Turkey',
                    'stars' => 4,
                    'address' => 'Kızılay Meydanı No:25',
                    'description' => 'İş seyahatleri için ideal',
                    'min_price' => 100.00,
                    'currency' => 'TRY',
                    'amenities' => ['WiFi', 'Business Center', 'Restaurant'],
                    'images' => ['business1.jpg']
                ],
                [
                    'id' => 'HTL005',
                    'name' => 'Historic Palace Hotel',
                    'city' => 'Istanbul',
                    'country' => 'Turkey',
                    'stars' => 5,
                    'address' => 'Sultanahmet Meydanı No:3',
                    'description' => 'Tarihi saray oteli',
                    'min_price' => 200.00,
                    'currency' => 'TRY',
                    'amenities' => ['WiFi', 'Historic', 'Restaurant', 'Spa'],
                    'images' => ['palace1.jpg', 'palace2.jpg']
                ]
            ];

            // Simüle edilmiş gecikme
            usleep(1000000); // 1 saniye

            return [
                'success' => true,
                'message' => count($mockHotels) . ' otel başarıyla çekildi',
                'data' => $mockHotels,
                'total_count' => count($mockHotels),
                'sync_time' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Mock API hotel sync failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Otel verileri çekilemedi: ' . $e->getMessage(),
                'data' => [],
                'total_count' => 0,
                'sync_time' => null
            ];
        }
    }

    /**
     * Belirli bir otelin detaylarını çeker
     */
    public function getHotelDetails(string $hotelId): array
    {
        try {
            // Mock hotel detail
            $hotelDetail = [
                'id' => $hotelId,
                'name' => 'Sample Hotel',
                'city' => 'Sample City',
                'country' => 'Turkey',
                'stars' => 4,
                'address' => 'Sample Address',
                'description' => 'Sample description',
                'min_price' => 100.00,
                'currency' => 'TRY',
                'amenities' => ['WiFi', 'Pool'],
                'images' => ['hotel.jpg'],
                'rooms' => [
                    [
                        'type' => 'Standard',
                        'capacity' => 2,
                        'price' => 100.00
                    ],
                    [
                        'type' => 'Deluxe',
                        'capacity' => 3,
                        'price' => 150.00
                    ]
                ]
            ];

            return [
                'success' => true,
                'message' => 'Otel detayları başarıyla çekildi',
                'data' => $hotelDetail
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Otel detayları çekilemedi: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Fiyat sorgulaması yapar
     */
    public function searchPrices(array $params): array
    {
        try {
            // Mock price search results
            $prices = [
                'hotel_id' => $params['hotel_id'] ?? 'HTL001',
                'check_in' => $params['check_in'] ?? '2024-01-01',
                'check_out' => $params['check_out'] ?? '2024-01-03',
                'guests' => $params['guests'] ?? 2,
                'rooms' => [
                    [
                        'type' => 'Standard',
                        'price' => 120.00,
                        'currency' => 'TRY',
                        'available' => true
                    ],
                    [
                        'type' => 'Deluxe',
                        'price' => 180.00,
                        'currency' => 'TRY',
                        'available' => true
                    ]
                ]
            ];

            return [
                'success' => true,
                'message' => 'Fiyat sorgulaması başarılı',
                'data' => $prices
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Fiyat sorgulaması başarısız: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
} 