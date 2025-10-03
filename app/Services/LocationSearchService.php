<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serwis wyszukiwania lokalizacji z obsługą lokalnego Nominatim.
 *
 * Zapewnia elastyczny system geocodingu z automatycznym fallback-iem
 * z lokalnej instancji Nominatim na zewnętrzne API w przypadku awarii.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class LocationSearchService
{
    private const EXTERNAL_NOMINATIM_URL = 'https://nominatim.openstreetmap.org';

    private const CACHE_TTL = 86400; // 24 hours

    private const REQUEST_DELAY = 1000; // 1 second delay between requests (Nominatim policy)

    private const LOCAL_REQUEST_DELAY = 100; // Faster for local instance

    /**
     * Wyszukuje lokalizacje z obsługą lokalnego Nominatim i fallback-u.
     *
     * @param  string  $query  Zapytanie wyszukiwania
     * @param  int  $limit  Maksymalna liczba wyników
     * @return array Lista znalezionych lokalizacji
     */
    public function searchLocations(string $query, int $limit = 10): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        // Używaj cache-u z wyjątkiem środowiska testowego
        if (config('app.env') === 'testing') {
            return $this->performLocationSearch($query, $limit);
        }

        $cacheKey = 'location_search_'.md5($query.$limit.$this->getNominatimUrl());

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($query, $limit) {
            return $this->performLocationSearch($query, $limit);
        });
    }

    public function searchHierarchical(string $query, int $limit = 10): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        // 🚀 Enhanced search with smart expansion for partial queries
        $results = $this->getExpandedSearchResults($query, $limit);

        return $this->organizeHierarchically($results, $query);
    }

    private function getExpandedSearchResults(string $query, int $limit): array
    {
        $allResults = [];

        // First, try exact search
        $exactResults = $this->searchLocations($query, $limit);
        $allResults = array_merge($allResults, $exactResults);

        // If we got some results or query is already long enough, return what we have
        if (count($exactResults) >= 3 || strlen($query) >= 6) {
            return array_slice($allResults, 0, $limit);
        }

        // For short queries, try to find matching Polish cities
        $expandedResults = $this->searchMatchingPolishCities($query, $limit - count($exactResults));
        $allResults = array_merge($allResults, $expandedResults);

        // Remove duplicates based on coordinates
        $unique = [];
        $seen = [];
        foreach ($allResults as $result) {
            $key = round($result['lat'], 4).'_'.round($result['lon'], 4);
            if (! isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $result;
            }
        }

        return array_slice($unique, 0, $limit);
    }

    private function searchMatchingPolishCities(string $query, int $limit): array
    {
        $query = mb_strtolower($query);
        $commonCities = [
            'olsztyn' => ['lat' => 53.7766839, 'lon' => 20.476507, 'voivodeship' => 'warmińsko-mazurskie'],
            'warszawa' => ['lat' => 52.2319581, 'lon' => 21.0067249, 'voivodeship' => 'mazowieckie'],
            'kraków' => ['lat' => 50.0469432, 'lon' => 19.9370471, 'voivodeship' => 'małopolskie'],
            'wrocław' => ['lat' => 51.1089776, 'lon' => 17.0326689, 'voivodeship' => 'dolnośląskie'],
            'poznań' => ['lat' => 52.4006932, 'lon' => 16.9299498, 'voivodeship' => 'wielkopolskie'],
            'gdańsk' => ['lat' => 54.3610255, 'lon' => 18.6335814, 'voivodeship' => 'pomorskie'],
            'szczecin' => ['lat' => 53.4356788, 'lon' => 14.5407222, 'voivodeship' => 'zachodniopomorskie'],
            'bydgoszcz' => ['lat' => 53.1181677, 'lon' => 18.0058315, 'voivodeship' => 'kujawsko-pomorskie'],
            'lublin' => ['lat' => 51.2385077, 'lon' => 22.5463748, 'voivodeship' => 'lubelskie'],
            'katowice' => ['lat' => 50.2598987, 'lon' => 19.0215852, 'voivodeship' => 'śląskie'],
            'białystok' => ['lat' => 53.1203847, 'lon' => 23.1614085, 'voivodeship' => 'podlaskie'],
            'gdynia' => ['lat' => 54.5202861, 'lon' => 18.5391599, 'voivodeship' => 'pomorskie'],
            'częstochowa' => ['lat' => 50.8058919, 'lon' => 19.1201227, 'voivodeship' => 'śląskie'],
            'radom' => ['lat' => 51.3906239, 'lon' => 21.1471353, 'voivodeship' => 'mazowieckie'],
            'toruń' => ['lat' => 53.0098834, 'lon' => 18.6067428, 'voivodeship' => 'kujawsko-pomorskie'],
            'sosnowiec' => ['lat' => 50.2863173, 'lon' => 19.1040649, 'voivodeship' => 'śląskie'],
            'kielce' => ['lat' => 50.8670209, 'lon' => 20.6286121, 'voivodeship' => 'świętokrzyskie'],
            'gliwice' => ['lat' => 50.2976226, 'lon' => 18.6765808, 'voivodeship' => 'śląskie'],
            'zabrze' => ['lat' => 50.3205401, 'lon' => 18.7857275, 'voivodeship' => 'śląskie'],
            'olkusz' => ['lat' => 50.2794944, 'lon' => 19.5633563, 'voivodeship' => 'małopolskie'],
        ];

        $matches = [];
        foreach ($commonCities as $cityName => $data) {
            if (str_contains($cityName, $query) && count($matches) < $limit) {
                $matches[] = [
                    'display_name' => ucfirst($cityName).', województwo '.$data['voivodeship'].', Polska',
                    'lat' => $data['lat'],
                    'lon' => $data['lon'],
                    'type' => 'city',
                    'city' => ucfirst($cityName),
                    'district' => '',
                    'state' => 'województwo '.$data['voivodeship'],
                    'country' => 'Polska',
                    'postcode' => '',
                    'importance' => 0.8, // High importance for common cities
                    'bbox' => null,
                    'osm_type' => 'synthetic',
                    'place_rank' => 12,
                ];
            }
        }

        return $matches;
    }

    /**
     * Wykonuje wyszukiwanie lokalizacji z automatycznym fallback-iem.
     *
     * @param  string  $query  Zapytanie wyszukiwania
     * @param  int  $limit  Maksymalna liczba wyników
     * @return array Lista znalezionych lokalizacji
     */
    private function performLocationSearch(string $query, int $limit): array
    {
        $allResults = [];

        // Spróbuj najpierw lokalnego Nominatim
        if ($this->isLocalNominatimEnabled()) {
            try {
                $allResults = $this->searchWithLocalNominatim($query, $limit);

                if (! empty($allResults)) {
                    Log::info('Local Nominatim search successful', [
                        'query' => $query,
                        'results_count' => count($allResults),
                        'source' => 'local_nominatim',
                    ]);

                    return $this->deduplicateResults($allResults, $limit);
                }
            } catch (\Exception $e) {
                Log::warning('Local Nominatim failed, trying fallback', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                    'fallback' => 'external_nominatim',
                ]);
            }
        }

        // Fallback do zewnętrznego Nominatim
        if ($this->isFallbackEnabled()) {
            try {
                $allResults = $this->searchWithExternalNominatim($query, $limit);

                if (! empty($allResults)) {
                    Log::info('External Nominatim search successful', [
                        'query' => $query,
                        'results_count' => count($allResults),
                        'source' => 'external_nominatim',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Both local and external Nominatim failed', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        }

        return $this->deduplicateResults($allResults, $limit);
    }

    private function formatLocationResults(array $rawResults): array
    {
        $formatted = [];

        foreach ($rawResults as $result) {
            $address = $result['address'] ?? [];

            $formatted[] = [
                'display_name' => $result['display_name'],
                'lat' => (float) $result['lat'],
                'lon' => (float) $result['lon'],
                'type' => $this->determineLocationType($result),
                'city' => $this->extractCity($address),
                'district' => $this->extractDistrict($address),
                'state' => $address['state'] ?? '',
                'country' => $address['country'] ?? '',
                'postcode' => $address['postcode'] ?? '',
                'importance' => (float) ($result['importance'] ?? 0),
                'bbox' => $result['boundingbox'] ?? null,
                'osm_type' => $result['osm_type'] ?? null,
                'place_rank' => (int) ($result['place_rank'] ?? 0),

                // Rozszerzone dane adresowe dla integracji z GUS
                'road' => $address['road'] ?? '',
                'house_number' => $address['house_number'] ?? '',
                'municipality' => $address['municipality'] ?? '',
                'county' => $address['county'] ?? '',
                'town' => $address['town'] ?? '',
                'village' => $address['village'] ?? '',

                // Nazwa dla GUS - priorytet: town > city > municipality > village
                'gus_city_name' => $this->extractGUSCityName($address),
            ];
        }

        // Sort by importance and place rank
        usort($formatted, function ($a, $b) {
            if ($a['importance'] !== $b['importance']) {
                return $b['importance'] <=> $a['importance'];
            }

            return $a['place_rank'] <=> $b['place_rank'];
        });

        return $formatted;
    }

    private function organizeHierarchically(array $results, string $query): array
    {
        $organized = [
            'cities' => [],
            'districts' => [],
            'other' => [],
        ];

        $queryLower = mb_strtolower($query);

        foreach ($results as $result) {
            $cityLower = mb_strtolower($result['city']);
            $districtLower = mb_strtolower($result['district']);

            // If query matches city name, prioritize city results
            if (str_contains($cityLower, $queryLower) || str_contains($queryLower, $cityLower)) {
                if ($result['type'] === 'city') {
                    $organized['cities'][] = $result;
                } elseif ($result['type'] === 'district' && ! empty($result['district'])) {
                    $organized['districts'][] = $result;
                } else {
                    $organized['other'][] = $result;
                }
            } else {
                $organized['other'][] = $result;
            }
        }

        // Build hierarchical response
        $hierarchical = [];

        // Add cities first
        foreach ($organized['cities'] as $city) {
            $hierarchical[] = [
                'type' => 'city',
                'label' => $city['city'],
                'display_name' => $city['display_name'],
                'coordinates' => [$city['lat'], $city['lon']],
                'data' => $city,
            ];
        }

        // Then add districts grouped by city
        $districtsByCity = [];
        foreach ($organized['districts'] as $district) {
            $cityName = $district['city'];
            if (! isset($districtsByCity[$cityName])) {
                $districtsByCity[$cityName] = [];
            }
            $districtsByCity[$cityName][] = $district;
        }

        foreach ($districtsByCity as $cityName => $districts) {
            foreach ($districts as $district) {
                $hierarchical[] = [
                    'type' => 'district',
                    'label' => $district['district'].', '.$cityName,
                    'display_name' => $district['display_name'],
                    'coordinates' => [$district['lat'], $district['lon']],
                    'parent_city' => $cityName,
                    'data' => $district,
                ];
            }
        }

        // Finally add other results
        foreach ($organized['other'] as $other) {
            $hierarchical[] = [
                'type' => $other['type'],
                'label' => $this->createLabel($other),
                'display_name' => $other['display_name'],
                'coordinates' => [$other['lat'], $other['lon']],
                'data' => $other,
            ];
        }

        return $hierarchical;
    }

    private function determineLocationType(array $result): string
    {
        $type = $result['type'] ?? '';
        $class = $result['class'] ?? '';
        $placeRank = (int) ($result['place_rank'] ?? 0);
        $address = $result['address'] ?? [];
        $addresstype = $result['addresstype'] ?? '';

        // Determine type based on addresstype first (most reliable)
        if ($addresstype === 'city' || $addresstype === 'town') {
            return 'city';
        }

        if (in_array($addresstype, ['suburb', 'neighbourhood', 'quarter', 'city_district'])) {
            return 'district';
        }

        // Check OSM type
        if (in_array($type, ['city', 'town'])) {
            return 'city';
        }

        if (in_array($type, ['suburb', 'neighbourhood', 'quarter', 'city_district'])) {
            return 'district';
        }

        if (in_array($type, ['village', 'hamlet'])) {
            return 'village';
        }

        // Check for administrative boundaries (cities often have type=administrative)
        if ($class === 'boundary' && $type === 'administrative') {
            if ($placeRank <= 12) {
                return 'city';
            } elseif ($placeRank <= 16) {
                return 'district';
            }
        }

        if ($class === 'place') {
            if ($placeRank <= 12) {
                return 'city';
            } elseif ($placeRank <= 16) {
                return 'district';
            }
        }

        return 'other';
    }

    private function extractCity(array $address): string
    {
        // Pierwsza próba - standardowe pola Nominatim w kolejności priorytetów dla Polski
        $primaryFields = [
            'city',           // Główne miasta
            'town',           // Mniejsze miasta
            'municipality',   // Gminy miejskie
            'village',        // Wsie
            'hamlet',          // Przysiółki
        ];

        foreach ($primaryFields as $field) {
            if (! empty($address[$field])) {
                $cityName = trim($address[$field]);

                // Walidacja - sprawdź czy to nie jest przypadkowy tekst
                if ($this->isValidCityName($cityName)) {
                    Log::info("🏙️ City extracted from field '{$field}': {$cityName}");

                    return $cityName;
                }
            }
        }

        // Druga próba - alternatywne pola dla Polski
        $alternativeFields = [
            'suburb',         // Dzielnice (mogą być używane jako miasta w małych obszarach)
            'neighbourhood',  // Osiedla
            'quarter',        // Kwartały
            'city_district',   // Dzielnice miejskie
        ];

        foreach ($alternativeFields as $field) {
            if (! empty($address[$field])) {
                $cityName = trim($address[$field]);

                if ($this->isValidCityName($cityName)) {
                    Log::info("🏘️ City extracted from alternative field '{$field}': {$cityName}");

                    return $cityName;
                }
            }
        }

        // Trzecia próba - ekstraktuj z display_name jeśli nic nie znaleziono
        if (! empty($address['display_name'])) {
            $extractedCity = $this->extractCityFromDisplayName($address['display_name']);
            if ($extractedCity) {
                Log::info("📍 City extracted from display_name: {$extractedCity}");

                return $extractedCity;
            }
        }

        Log::warning('⚠️ No valid city found in address data', ['address_keys' => array_keys($address)]);

        return '';
    }

    /**
     * Waliduje czy nazwa miasta jest poprawna
     */
    private function isValidCityName(string $name): bool
    {
        if (strlen($name) < 2) {
            return false;
        }

        // Odrzuć oczywiste nie-miasta
        $invalidPatterns = [
            '/^[0-9\-\s]+$/',           // Same cyfry, myślniki, spacje
            '/^(ul\.|al\.|pl\.|os\.)/i', // Prefiksy ulic
            '/^(województwo|powiat|gmina)\s/i', // Prefiksy administracyjne
            '/^\d{2}-\d{3}$/',          // Kody pocztowe
        ];

        foreach ($invalidPatterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ekstraktuje miasto z display_name jako ostatnia deska ratunku
     */
    private function extractCityFromDisplayName(string $displayName): ?string
    {
        // Podziel na części przez przecinki
        $parts = array_map('trim', explode(',', $displayName));

        // Usuń części które na pewno nie są miastami
        $filteredParts = array_filter($parts, function ($part) {
            return $this->isValidCityName($part) &&
                   ! preg_match('/^(województwo|powiat|gmina|woj\.)/i', $part) &&
                   strlen($part) > 2 &&
                   strlen($part) < 50; // Nazwy miast nie powinny być bardzo długie
        });

        // Weź pierwszą sensowną część (często jest to miasto)
        if (! empty($filteredParts)) {
            $cityCandidate = reset($filteredParts);

            // Dodatkowa walidacja - usuń prefiksy
            $cityCandidate = preg_replace('/^(ul\.|al\.|pl\.|os\.)\s*/i', '', $cityCandidate);
            $cityCandidate = trim($cityCandidate);

            if ($this->isValidCityName($cityCandidate)) {
                return $cityCandidate;
            }
        }

        return null;
    }

    /**
     * Ekstraktuje nazwę miasta dla API GUS (priorytet: town > city > municipality > village).
     *
     * GUS wymaga konkretnej nazwy jednostki. Dla obszarów miejskich najlepsze są:
     * - town (miejscowości typu Łomianki)
     * - city (duże miasta)
     * - municipality (gminy, jeśli brak town/city)
     * - village (wsie)
     *
     * @param  array  $address  Dane adresowe z Nominatim
     * @return string Nazwa miasta dla GUS
     */
    private function extractGUSCityName(array $address): string
    {
        // Priorytet 1: town - małe miasta i miasteczka (np. Łomianki)
        if (! empty($address['town']) && $this->isValidCityName($address['town'])) {
            Log::debug('🏙️ GUS city name from town', ['name' => $address['town']]);

            return trim($address['town']);
        }

        // Priorytet 2: city - duże miasta (np. Warszawa, Kraków)
        if (! empty($address['city']) && $this->isValidCityName($address['city'])) {
            Log::debug('🏙️ GUS city name from city', ['name' => $address['city']]);

            return trim($address['city']);
        }

        // Priorytet 3: municipality - gminy (usuń prefix "gmina")
        if (! empty($address['municipality'])) {
            $municipality = preg_replace('/^gmina\s+/i', '', $address['municipality']);
            if ($this->isValidCityName($municipality)) {
                Log::debug('🏙️ GUS city name from municipality', ['name' => $municipality]);

                return trim($municipality);
            }
        }

        // Priorytet 4: village - wsie
        if (! empty($address['village']) && $this->isValidCityName($address['village'])) {
            Log::debug('🏙️ GUS city name from village', ['name' => $address['village']]);

            return trim($address['village']);
        }

        // Fallback - użyj extractCity
        $fallbackCity = $this->extractCity($address);
        Log::warning('⚠️ GUS city name fallback used', ['name' => $fallbackCity]);

        return $fallbackCity;
    }

    private function extractDistrict(array $address): string
    {
        $districtFields = ['suburb', 'neighbourhood', 'quarter', 'city_district'];

        foreach ($districtFields as $field) {
            if (! empty($address[$field])) {
                return $address[$field];
            }
        }

        return '';
    }

    private function createLabel(array $location): string
    {
        // For cities, prefer simple city name over complex labels
        if (! empty($location['city'])) {
            return $location['city'];
        }

        // For districts with city context
        if (! empty($location['district']) && ! empty($location['city'])) {
            return $location['district'].', '.$location['city'];
        }

        // For other types, use simplified approach - avoid "województwo" in main label
        $parts = array_filter([
            $location['district'],
            $location['city'],
        ]);

        // If we have parts, use them; otherwise fall back to display name
        return ! empty($parts) ? implode(', ', $parts) : $location['display_name'];
    }

    public function getLocationDetails(float $lat, float $lon): ?array
    {
        $cacheKey = 'location_details_'.md5($lat.'_'.$lon);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lat, $lon) {
            return $this->performReverseGeocode($lat, $lon);
        });
    }

    public function getCoordinates(string $query): ?array
    {
        $cacheKey = "coordinates:{$query}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query) {
            $results = $this->searchLocations($query, 1);

            if (empty($results)) {
                return null;
            }

            $result = $results[0];

            return [
                'lat' => (float) $result['lat'],
                'lng' => (float) $result['lon'],
                'display_name' => $result['display_name'],
                'type' => $this->determineLocationType($result),
            ];
        });
    }

    // ===== NOWE METODY DLA LOKALNEGO NOMINATIM =====

    /**
     * Sprawdza czy lokalny Nominatim jest włączony.
     */
    private function isLocalNominatimEnabled(): bool
    {
        return config('app.nominatim_local_enabled', false) === true;
    }

    /**
     * Sprawdza czy fallback do zewnętrznego API jest włączony.
     */
    private function isFallbackEnabled(): bool
    {
        return config('app.nominatim_fallback_enabled', true) === true;
    }

    /**
     * Zwraca URL do Nominatim API (lokalny lub zewnętrzny).
     */
    private function getNominatimUrl(): string
    {
        if ($this->isLocalNominatimEnabled()) {
            return config('app.nominatim_local_url', 'http://localhost:8080');
        }

        return self::EXTERNAL_NOMINATIM_URL;
    }

    /**
     * Zwraca TTL cache-u.
     */
    private function getCacheTtl(): int
    {
        return config('app.nominatim_cache_ttl', self::CACHE_TTL);
    }

    /**
     * Zwraca opóźnienie między requestami.
     *
     * @return int Opóźnienie w mikrosekundach
     */
    private function getRequestDelay(): int
    {
        if ($this->isLocalNominatimEnabled()) {
            return config('app.nominatim_rate_limit_delay', self::LOCAL_REQUEST_DELAY) * 1000;
        }

        return self::REQUEST_DELAY * 1000;
    }

    /**
     * Wyszukuje lokalizacje używając lokalnego Nominatim.
     *
     * @param  string  $query  Zapytanie wyszukiwania
     * @param  int  $limit  Maksymalna liczba wyników
     * @return array Lista znalezionych lokalizacji
     *
     * @throws \Exception
     */
    private function searchWithLocalNominatim(string $query, int $limit): array
    {
        $localUrl = config('app.nominatim_local_url', 'http://localhost:8080');

        // Sprawdź czy lokalny Nominatim jest dostępny
        if (! $this->isLocalNominatimHealthy()) {
            throw new \Exception('Local Nominatim is not healthy');
        }

        // Dodaj opóźnienie (krótsze dla lokalnego)
        usleep($this->getRequestDelay());

        $response = Http::timeout(30) // Dłuższy timeout dla lokalnego
            ->withHeaders([
                'User-Agent' => 'PetHelp/1.0 (contact@pethelp.test)',
            ])
            ->get($localUrl.'/search', [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => $limit,
                'countrycodes' => 'pl', // Focus na Polskę
                'dedupe' => 1,
                'extratags' => 1,
            ]);

        if (! $response->successful()) {
            throw new \Exception("Local Nominatim request failed: {$response->status()} - {$response->body()}");
        }

        return $this->formatLocationResults($response->json());
    }

    /**
     * Wyszukuje lokalizacje używając zewnętrznego Nominatim.
     *
     * @param  string  $query  Zapytanie wyszukiwania
     * @param  int  $limit  Maksymalna liczba wyników
     * @return array Lista znalezionych lokalizacji
     *
     * @throws \Exception
     */
    private function searchWithExternalNominatim(string $query, int $limit): array
    {
        // Dodaj opóźnienie (zgodnie z polityką użytkowania)
        usleep($this->getRequestDelay());

        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => 'PetHelp/1.0 (contact@pethelp.test)',
            ])
            ->get(self::EXTERNAL_NOMINATIM_URL.'/search', [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => $limit,
                'countrycodes' => 'pl', // Focus na Polskę
                'dedupe' => 1,
                'extratags' => 1,
            ]);

        if (! $response->successful()) {
            throw new \Exception("External Nominatim request failed: {$response->status()} - {$response->body()}");
        }

        return $this->formatLocationResults($response->json());
    }

    /**
     * Sprawdza health lokalnego Nominatim.
     */
    private function isLocalNominatimHealthy(): bool
    {
        try {
            $localUrl = config('app.nominatim_local_url', 'http://localhost:8080');

            $response = Http::timeout(5)->get($localUrl.'/status');

            return $response->successful();
        } catch (\Exception $e) {
            Log::debug('Local Nominatim health check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Usuwa duplikaty z wyników wyszukiwania.
     *
     * @param  array  $results  Lista wyników
     * @param  int  $limit  Maksymalna liczba wyników
     * @return array Unikalne wyniki
     */
    private function deduplicateResults(array $results, int $limit): array
    {
        $uniqueResults = [];
        $seen = [];

        foreach ($results as $result) {
            $key = $result['lat'].'_'.$result['lon'];
            if (! isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueResults[] = $result;
                if (count($uniqueResults) >= $limit) {
                    break;
                }
            }
        }

        return $uniqueResults;
    }

    /**
     * Aktualizuje performReverseGeocode do obsługi lokalnego Nominatim.
     *
     * @param  float  $lat  Szerokość geograficzna
     * @param  float  $lon  Długość geograficzna
     * @return array|null Szczegóły lokalizacji
     */
    private function performReverseGeocode(float $lat, float $lon): ?array
    {
        try {
            $nominatimUrl = $this->getNominatimUrl();

            // Sprawdź czy lokalny Nominatim jest dostępny (jeśli jest włączony)
            if ($this->isLocalNominatimEnabled() && ! $this->isLocalNominatimHealthy()) {
                if (! $this->isFallbackEnabled()) {
                    return null;
                }
                $nominatimUrl = self::EXTERNAL_NOMINATIM_URL;
            }

            // Dodaj opóźnienie
            usleep($this->getRequestDelay());

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'PetHelp/1.0 (contact@pethelp.test)',
                ])
                ->get($nominatimUrl.'/reverse', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'zoom' => 18,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (! empty($result)) {
                    return $this->formatLocationResults([$result])[0] ?? null;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Reverse geocoding failed', [
                'lat' => $lat,
                'lon' => $lon,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
