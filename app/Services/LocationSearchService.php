<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationSearchService
{
    private const NOMINATIM_BASE_URL = 'https://nominatim.openstreetmap.org';

    private const CACHE_TTL = 86400; // 24 hours

    private const REQUEST_DELAY = 1000; // 1 second delay between requests (Nominatim policy)

    public function searchLocations(string $query, int $limit = 10): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        // Disable cache for testing environment
        if (config('app.env') === 'local') {
            return $this->performLocationSearch($query, $limit);
        }

        $cacheKey = 'location_search_'.md5($query.$limit);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $limit) {
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

    private function performLocationSearch(string $query, int $limit): array
    {
        try {
            // Direct search - bez expansion logic (simplified approach)
            // Add delay to respect Nominatim usage policy
            usleep(self::REQUEST_DELAY * 1000);

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'PetHelp/1.0 (contact@pethelp.test)',
                ])
                ->get(self::NOMINATIM_BASE_URL.'/search', [
                    'q' => $query,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => $limit,
                    'countrycodes' => 'pl', // Focus on Poland
                    'dedupe' => 1,
                    'extratags' => 1,
                ]);

            $allResults = [];
            if ($response->successful()) {
                $allResults = $this->formatLocationResults($response->json());
            } else {
                Log::warning('Nominatim API request failed', [
                    'query' => $query,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }

            // Remove duplicates and limit results
            $uniqueResults = [];
            $seen = [];
            foreach ($allResults as $result) {
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

        } catch (\Exception $e) {
            Log::error('Location search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
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
        $cityFields = ['city', 'town', 'municipality', 'village', 'hamlet'];

        foreach ($cityFields as $field) {
            if (! empty($address[$field])) {
                return $address[$field];
            }
        }

        return '';
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

    private function performReverseGeocode(float $lat, float $lon): ?array
    {
        try {
            // Add delay to respect Nominatim usage policy
            usleep(self::REQUEST_DELAY * 1000);

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'PetHelp/1.0 (contact@pethelp.test)',
                ])
                ->get(self::NOMINATIM_BASE_URL.'/reverse', [
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
}
