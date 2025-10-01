<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Serwis do analizy cen usług pet sitterów w danej lokalizacji.
 *
 * Pobiera dane z bazy danych i oblicza statystyki cenowe
 * (minimum, maksimum, średnia) dla różnych typów usług
 * w promieniu określonym od lokalizacji użytkownika.
 *
 * @package App\Services
 * @author Claude AI Assistant
 * @version 1.0.0
 */
class PricingAnalysisService
{
    /**
     * Domyślny promień wyszukiwania w kilometrach.
     */
    private const DEFAULT_RADIUS_KM = 20;

    /**
     * Czas cache'owania wyników w minutach.
     */
    private const CACHE_DURATION_MINUTES = 60;

    /**
     * Minimalna liczba próbek do uznania danych za wiarygodne.
     */
    private const MIN_SAMPLE_SIZE = 3;

    /**
     * Domyślne ceny fallback gdy brak danych w systemie.
     */
    private const DEFAULT_PRICES = [
        'dog_walking' => ['min' => 25, 'max' => 45, 'avg' => 35],
        'pet_sitting' => ['min' => 20, 'max' => 35, 'avg' => 28],
        'pet_boarding' => ['min' => 70, 'max' => 100, 'avg' => 85],
        'overnight_care' => ['min' => 100, 'max' => 150, 'avg' => 120],
        'pet_transport' => ['min' => 1.5, 'max' => 3, 'avg' => 2],
        'vet_visits' => ['min' => 40, 'max' => 70, 'avg' => 50],
    ];

    /**
     * Analizuje ceny usług w okolicy na podstawie lokalizacji.
     *
     * @param float|null $latitude Szerokość geograficzna
     * @param float|null $longitude Długość geograficzna
     * @param int $radiusKm Promień wyszukiwania w km
     * @return array Analiza cenowa dla każdego typu usługi
     */
    public function analyzePricing(?float $latitude, ?float $longitude, int $radiusKm = self::DEFAULT_RADIUS_KM): array
    {
        // Jeśli brak lokalizacji, zwróć domyślne ceny
        if (!$latitude || !$longitude) {
            Log::info('📊 Brak lokalizacji - używam domyślnych cen');
            return $this->getDefaultPricesWithMetadata();
        }

        // Cache key bazujący na lokalizacji
        $cacheKey = "pricing_analysis_{$latitude}_{$longitude}_{$radiusKm}";

        return Cache::remember($cacheKey, self::CACHE_DURATION_MINUTES * 60, function () use ($latitude, $longitude, $radiusKm) {
            Log::info('📊 Analiza cen dla lokalizacji', [
                'lat' => $latitude,
                'lng' => $longitude,
                'radius' => $radiusKm
            ]);

            $analysis = [];

            // Pobierz ceny dla każdego typu usługi
            foreach (array_keys(self::DEFAULT_PRICES) as $serviceType) {
                $stats = $this->getServicePriceStats($serviceType, $latitude, $longitude, $radiusKm);
                $analysis[$serviceType] = $stats;
            }

            return $analysis;
        });
    }

    /**
     * Pobiera statystyki cen dla konkretnego typu usługi.
     *
     * @param string $serviceType Typ usługi
     * @param float $latitude Szerokość geograficzna
     * @param float $longitude Długość geograficzna
     * @param int $radiusKm Promień wyszukiwania
     * @return array Statystyki cenowe
     */
    private function getServicePriceStats(string $serviceType, float $latitude, float $longitude, int $radiusKm): array
    {
        try {
            // Formuła Haversine do obliczenia odległości
            $prices = DB::table('user_profiles')
                ->join('users', 'user_profiles.user_id', '=', 'users.id')
                ->whereNotNull('user_profiles.latitude')
                ->whereNotNull('user_profiles.longitude')
                ->whereNotNull("user_profiles.pricing->{$serviceType}")
                ->where('users.is_active', true)
                ->selectRaw("
                    user_profiles.pricing->'{$serviceType}' as price,
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(user_profiles.latitude)) *
                        cos(radians(user_profiles.longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(user_profiles.latitude))
                    )) AS distance
                ", [$latitude, $longitude, $latitude])
                ->havingRaw('distance <= ?', [$radiusKm])
                ->pluck('price')
                ->map(fn($price) => (float) $price)
                ->filter(fn($price) => $price > 0)
                ->values();

            // Jeśli mamy wystarczająco danych
            if ($prices->count() >= self::MIN_SAMPLE_SIZE) {
                $min = $prices->min();
                $max = $prices->max();
                $avg = round($prices->avg(), 2);
                $count = $prices->count();

                Log::info("📊 Znaleziono dane dla {$serviceType}", [
                    'count' => $count,
                    'min' => $min,
                    'max' => $max,
                    'avg' => $avg
                ]);

                return [
                    'min' => $min,
                    'max' => $max,
                    'avg' => $avg,
                    'sample_size' => $count,
                    'source' => 'database',
                    'reliable' => true
                ];
            }

            // Brak wystarczających danych - użyj domyślnych
            Log::info("📊 Za mało danych dla {$serviceType} (znaleziono: {$prices->count()}), używam domyślnych");
            return $this->getDefaultPriceForService($serviceType);

        } catch (\Exception $e) {
            Log::error("📊 Błąd analizy cen dla {$serviceType}: " . $e->getMessage());
            return $this->getDefaultPriceForService($serviceType);
        }
    }

    /**
     * Zwraca domyślne ceny dla usługi.
     *
     * @param string $serviceType Typ usługi
     * @return array Domyślne statystyki cenowe
     */
    private function getDefaultPriceForService(string $serviceType): array
    {
        $defaults = self::DEFAULT_PRICES[$serviceType] ?? ['min' => 0, 'max' => 0, 'avg' => 0];

        return [
            'min' => $defaults['min'],
            'max' => $defaults['max'],
            'avg' => $defaults['avg'],
            'sample_size' => 0,
            'source' => 'default',
            'reliable' => false
        ];
    }

    /**
     * Zwraca wszystkie domyślne ceny z metadanymi.
     *
     * @return array Domyślne ceny dla wszystkich usług
     */
    private function getDefaultPricesWithMetadata(): array
    {
        $result = [];

        foreach (self::DEFAULT_PRICES as $serviceType => $prices) {
            $result[$serviceType] = [
                'min' => $prices['min'],
                'max' => $prices['max'],
                'avg' => $prices['avg'],
                'sample_size' => 0,
                'source' => 'default',
                'reliable' => false
            ];
        }

        return $result;
    }

    /**
     * Oblicza sugerowaną cenę dla usługi na podstawie strategii cenowej.
     *
     * @param string $serviceType Typ usługi
     * @param float|null $latitude Szerokość geograficzna
     * @param float|null $longitude Długość geograficzna
     * @param string $strategy Strategia cenowa (budget/competitive/premium)
     * @return float Sugerowana cena
     */
    public function getSuggestedPrice(string $serviceType, ?float $latitude, ?float $longitude, string $strategy = 'competitive'): float
    {
        $analysis = $this->analyzePricing($latitude, $longitude);
        $stats = $analysis[$serviceType] ?? null;

        if (!$stats) {
            return 0;
        }

        // Dobierz cenę według strategii
        switch ($strategy) {
            case 'budget':
                // 20% poniżej średniej lub minimum
                return round($stats['avg'] * 0.8);

            case 'premium':
                // 20% powyżej średniej
                return round($stats['avg'] * 1.2);

            case 'competitive':
            default:
                // Średnia rynkowa
                return round($stats['avg']);
        }
    }

    /**
     * Zwraca podsumowanie analizy rynku w formie tekstowej.
     *
     * @param float|null $latitude Szerokość geograficzna
     * @param float|null $longitude Długość geograficzna
     * @return array Podsumowanie analizy
     */
    public function getMarketSummary(?float $latitude, ?float $longitude): array
    {
        $analysis = $this->analyzePricing($latitude, $longitude);

        $summary = [];
        $totalSamples = 0;
        $reliableServices = 0;

        foreach ($analysis as $serviceType => $stats) {
            $totalSamples += $stats['sample_size'];
            if ($stats['reliable']) {
                $reliableServices++;
            }
        }

        return [
            'analysis' => $analysis,
            'total_samples' => $totalSamples,
            'reliable_services' => $reliableServices,
            'total_services' => count($analysis),
            'has_location' => ($latitude && $longitude) ? true : false,
            'data_quality' => $totalSamples >= self::MIN_SAMPLE_SIZE * count($analysis) ? 'high' : 'low'
        ];
    }
}
