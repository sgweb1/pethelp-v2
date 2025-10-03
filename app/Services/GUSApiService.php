<?php

namespace App\Services;

use App\Models\PopulationCoefficient;
use App\Models\PopulationGrid;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Serwis do estymacji populacji i potencjalnych klientów.
 *
 * Wykorzystuje siatki populacyjne (gridy 1km²) z Eurostat GEOSTAT
 * do precyzyjnego szacowania liczby potencjalnych klientów w promieniu.
 *
 * Wersja 3.0 wprowadza zaawansowane współczynniki korekcyjne uwzględniające:
 * - Wielkość miasta (kategorie X0-X4)
 * - Obecność uniwersytetów (studenci)
 * - Ruch turystyczny
 * - Ruch dojazdowy do pracy
 * - Gęstość zabudowy
 *
 * @see POPULATION_ESTIMATION_COEFFICIENTS.md - pełna dokumentacja współczynników
 *
 * @author Claude AI Assistant
 *
 * @version 3.0.0
 */
class GUSApiService
{
    /**
     * Konstruktor serwisu.
     */
    public function __construct(
        private readonly LocationSearchService $locationService
    ) {}

    /**
     * Oblicza potencjalną liczbę klientów w danym obszarze.
     *
     * Używa siatek populacyjnych (1km²) do dokładnego oszacowania populacji
     * w promieniu obsługi, niezależnie od granic administracyjnych.
     *
     * Wersja 3.0 - Formuła z współczynnikami korekcyjnymi:
     * 1. Znajdź wszystkie kratki 1km² w promieniu
     * 2. Zsumuj populację we wszystkich kratkach (G)
     * 3. Wykryj kontekst lokalizacji (miasto, uniwersytet, turystyka)
     * 4. Pobierz współczynniki korekcyjne z bazy danych
     * 5. Oblicz skorygowaną populację: adjusted = G × k_base × (1 + K_students + K_tourism + ...)
     * 6. Zastosuj współczynniki końcowe:
     *    - 37% Polaków ma zwierzęta domowe (dane GUS 2023)
     *    - 25% właścicieli zwierząt szuka profesjonalnej opieki minimum raz w roku
     *
     * @param  float  $latitude  Szerokość geograficzna
     * @param  float  $longitude  Długość geograficzna
     * @param  int  $radiusKm  Promień obsługi w kilometrach
     * @return int Szacowana liczba potencjalnych klientów
     */
    public function estimatePotentialClients(float $latitude, float $longitude, int $radiusKm): int
    {
        try {
            Log::info('🔍 Rozpoczęto estymację', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'radius_km' => $radiusKm,
            ]);

            // Znajdź wszystkie kratki w promieniu
            $grids = PopulationGrid::findInRadius($latitude, $longitude, $radiusKm);

            Log::info('📊 Znaleziono kratki', [
                'count' => $grids->count(),
            ]);

            if ($grids->isEmpty()) {
                Log::warning('❌ Brak danych gridowych, użyto fallback estymacji', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius_km' => $radiusKm,
                ]);

                return $this->estimateByRadius($radiusKm);
            }

            // Zsumuj populację we wszystkich kratkach (G)
            $gridPopulation = PopulationGrid::totalPopulation($grids);

            // Wykryj kontekst lokalizacji (miasto, charakterystyki)
            $context = $this->detectCityContext($latitude, $longitude, $gridPopulation);

            // Pobierz współczynniki korekcyjne na podstawie wielkości miasta
            $correctedPopulation = $this->applyCorrectionCoefficients($gridPopulation, $context);

            // Współczynniki końcowe:
            // - 37% Polaków ma zwierzęta domowe (dane GUS 2023)
            // - 25% właścicieli zwierząt szuka profesjonalnej opieki minimum raz w roku
            $petOwnershipRate = 0.37;
            $careSeekingRate = 0.25;

            $potentialClients = $correctedPopulation * $petOwnershipRate * $careSeekingRate;

            Log::info('Estymacja potencjalnych klientów (gridy populacyjne + współczynniki)', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'radius_km' => $radiusKm,
                'grids_found' => $grids->count(),
                'grid_population' => $gridPopulation,
                'corrected_population' => round($correctedPopulation),
                'correction_factor' => round($correctedPopulation / max(1, $gridPopulation), 4),
                'city_category' => $context['category'] ?? 'unknown',
                'potential_clients' => round($potentialClients),
            ]);

            return max(50, round($potentialClients)); // Minimum 50 potencjalnych klientów
        } catch (\Exception $e) {
            Log::error('Błąd podczas estymacji z gridów, użyto fallback', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'radius_km' => $radiusKm,
                'error' => $e->getMessage(),
            ]);

            return $this->estimateByRadius($radiusKm);
        }
    }

    /**
     * Wykrywa kontekst lokalizacji (miasto, charakterystyki).
     *
     * Analizuje lokalizację i zwraca informacje o charakterystykach miasta:
     * - Wielkość (populacja w promieniu)
     * - Kategoria (X0-X4)
     * - Czy ma uniwersytet
     * - Czy jest miejscem turystycznym
     * - Czy jest centrum dojazdowym
     *
     * @param  float  $latitude  Szerokość geograficzna
     * @param  float  $longitude  Długość geograficzna
     * @param  int  $gridPopulation  Populacja z gridów
     * @return array Kontekst lokalizacji
     */
    private function detectCityContext(float $latitude, float $longitude, int $gridPopulation): array
    {
        // Cache kontekstu na 24h (miasta rzadko zmieniają charakterystyki)
        $cacheKey = "city_context:{$latitude}:{$longitude}";

        return Cache::remember($cacheKey, 86400, function () use ($latitude, $longitude, $gridPopulation) {
            $context = [
                'population' => $gridPopulation,
                'category' => $this->determineCityCategory($gridPopulation),
                'has_university' => false,
                'is_tourist_destination' => false,
                'is_commuter_hub' => false,
                'building_density_ratio' => 1.0,
            ];

            // Pobierz nazwę miasta z reverse geocoding
            $locationDetails = $this->locationService->getLocationDetails($latitude, $longitude);

            if ($locationDetails) {
                $cityName = mb_strtolower($locationDetails['city'] ?? '');

                // Wykryj uniwersytety (prosta heurystyka - można rozszerzyć o bazę danych)
                $context['has_university'] = $this->hasUniversity($cityName, $gridPopulation);

                // Wykryj miejsca turystyczne (można rozszerzyć o dane z API)
                $context['is_tourist_destination'] = $this->isTouristDestination($cityName);

                // Wykryj centra dojazdowe (duże miasta regionalne)
                $context['is_commuter_hub'] = $this->isCommuterHub($cityName, $gridPopulation);
            }

            Log::debug('Wykryto kontekst miasta', $context);

            return $context;
        });
    }

    /**
     * Określa kategorię miasta na podstawie populacji.
     *
     * @param  int  $population  Populacja
     * @return string Kategoria (X0-X4)
     */
    private function determineCityCategory(int $population): string
    {
        if ($population < 5000) {
            return 'X0';
        } elseif ($population < 30000) {
            return 'X1';
        } elseif ($population < 100000) {
            return 'X2';
        } elseif ($population < 500000) {
            return 'X3';
        }

        return 'X4';
    }

    /**
     * Sprawdza czy miasto ma uniwersytet.
     *
     * Wykorzystuje rozszerzoną listę miast uniwersyteckich z pliku konfiguracyjnego
     * oraz heurystykę dla większych miast.
     *
     * @param  string  $cityName  Nazwa miasta
     * @param  int  $population  Populacja
     */
    private function hasUniversity(string $cityName, int $population): bool
    {
        // Pobierz listę miast uniwersyteckich z konfiguracji
        $universityCities = config('city_characteristics.university_cities', []);

        // Jeśli miasto jest na liście uniwersyteckiej
        if (in_array($cityName, $universityCities)) {
            return true;
        }

        // Heurystyka: miasta >50k mieszkańców często mają jakieś uczelnie
        if ($population > 50000) {
            return true;
        }

        return false;
    }

    /**
     * Sprawdza czy miasto jest miejscem turystycznym.
     *
     * Wykorzystuje rozszerzoną listę destynacji turystycznych z pliku konfiguracyjnego.
     * Lista zawiera:
     * - Miasta historyczne i kulturowe
     * - Kurorty nadmorskie (Bałtyk)
     * - Kurorty górskie (Tatry, Sudety, Beskidy)
     * - Miejsca pielgrzymkowe
     * - Uzdrowiska
     * - Regiony jezior
     *
     * @param  string  $cityName  Nazwa miasta
     */
    private function isTouristDestination(string $cityName): bool
    {
        // Pobierz listę destynacji turystycznych z konfiguracji
        $touristDestinations = config('city_characteristics.tourist_destinations', []);

        return in_array($cityName, $touristDestinations);
    }

    /**
     * Sprawdza czy miasto jest centrum dojazdowym.
     *
     * Wykorzystuje rozszerzoną listę centrów dojazdowych z pliku konfiguracyjnego.
     * Lista zawiera:
     * - Stolice województw
     * - Duże miasta >100k mieszkańców
     * - Ważne centra przemysłowe i biznesowe
     *
     * @param  string  $cityName  Nazwa miasta
     * @param  int  $population  Populacja
     */
    private function isCommuterHub(string $cityName, int $population): bool
    {
        // Pobierz listę centrów dojazdowych z konfiguracji
        $commuterHubs = config('city_characteristics.commuter_hubs', []);

        // Jeśli miasto jest na liście centrów dojazdowych
        if (in_array($cityName, $commuterHubs)) {
            return true;
        }

        // Heurystyka: miasta >100k są często centrami dojazdowymi
        if ($population > 100000) {
            return true;
        }

        return false;
    }

    /**
     * Stosuje współczynniki korekcyjne do populacji z gridów.
     *
     * @param  int  $gridPopulation  Populacja z gridów (G)
     * @param  array  $context  Kontekst lokalizacji
     * @return float Skorygowana populacja
     */
    private function applyCorrectionCoefficients(int $gridPopulation, array $context): float
    {
        // Pobierz współczynniki dla kategorii miasta
        $coefficient = PopulationCoefficient::forCategory($context['category']);

        if (! $coefficient) {
            // Fallback - brak korekty jeśli nie znaleziono współczynników
            Log::warning('Nie znaleziono współczynników dla kategorii', [
                'category' => $context['category'],
            ]);

            return $gridPopulation;
        }

        // Oblicz całkowitą korektę używając modelu
        $totalCorrectionFactor = $coefficient->calculateTotalCorrection($context);

        // Zastosuj korektę: corrected = G × total_correction_factor
        $correctedPopulation = $gridPopulation * $totalCorrectionFactor;

        Log::debug('Zastosowano współczynniki korekcyjne', [
            'grid_population' => $gridPopulation,
            'category' => $context['category'],
            'k_base' => $coefficient->getAverageKBase(),
            'total_correction_factor' => round($totalCorrectionFactor, 4),
            'corrected_population' => round($correctedPopulation),
            'context' => $context,
        ]);

        return $correctedPopulation;
    }

    /**
     * Estymacja bazująca tylko na promieniu (fallback).
     *
     * Używana gdy brak danych gridowych lub wystąpił błąd.
     * Opiera się na średniej gęstości zaludnienia Polski.
     *
     * @param  int  $radiusKm  Promień w kilometrach
     * @return int Szacowana liczba klientów
     */
    private function estimateByRadius(int $radiusKm): int
    {
        // Przybliżenie bazujące na średniej gęstości zaludnienia Polski (123 os/km²)
        $averageDensity = 123;
        $area = pi() * pow($radiusKm, 2);
        $population = $area * $averageDensity;

        $petOwnershipRate = 0.37;
        $careSeekingRate = 0.25;

        Log::info('Użyto fallback estymacji na podstawie średniej gęstości', [
            'radius_km' => $radiusKm,
            'area_km2' => round($area, 2),
            'estimated_population' => round($population),
            'potential_clients' => round($population * $petOwnershipRate * $careSeekingRate),
        ]);

        return max(50, round($population * $petOwnershipRate * $careSeekingRate));
    }
}
