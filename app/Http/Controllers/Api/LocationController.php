<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationSearchService;
use App\Services\AI\LocalAIAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function __construct(
        private LocationSearchService $locationService,
        private LocalAIAssistant $aiAssistant
    ) {}

    /**
     * Wyszukuje lokalizacje z obsługą lokalnego Nominatim.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        Log::info('Location search API request', [
            'query' => $query,
            'limit' => $limit,
            'source' => $this->getActiveSource()
        ]);

        $results = $this->locationService->searchLocations($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'query' => $query,
                'count' => count($results),
                'limit' => $limit,
                'source' => $this->getActiveSource()
            ],
        ]);
    }

    /**
     * Wykonuje reverse geocoding z obsługą lokalnego Nominatim.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reverse(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        $lat = (float) $request->input('lat');
        $lon = (float) $request->input('lon');

        Log::info('Reverse geocoding API request', [
            'lat' => $lat,
            'lon' => $lon,
            'source' => $this->getActiveSource()
        ]);

        $result = $this->locationService->getLocationDetails($lat, $lon);

        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result,
                'meta' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'source' => $this->getActiveSource()
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Location not found',
        ], 404);
    }

    /**
     * Estymuje populację w danym obszarze używając AI.
     *
     * Endpoint wykorzystujący lokalne AI (Ollama) do inteligentnej analizy
     * demograficznej obszaru. Zastępuje niestabilne API zewnętrzne.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function estimatePopulation(Request $request): JsonResponse
    {
        try {
            // Walidacja parametrów wejściowych
            $validated = $request->validate([
                'city' => 'required|string|min:2|max:100',
                'radius' => 'required|numeric|min:0.5|max:50',
                'address' => 'nullable|string|max:200', // opcjonalny pełny adres
            ]);

            $city = trim($validated['city']);
            $radius = (float) $validated['radius'];
            $address = $validated['address'] ?? null;

            Log::info('🤖 AI Population Estimation API call', [
                'city' => $city,
                'radius' => $radius,
                'address' => $address,
                'ip' => $request->ip()
            ]);

            // Wywołaj AI Assistant
            $populationData = $this->aiAssistant->estimatePopulation($city, $radius);

            // Dodaj metadane API
            $response = [
                'success' => true,
                'data' => $populationData,
                'meta' => [
                    'city' => $city,
                    'radius' => $radius,
                    'area_km2' => round(pi() * $radius * $radius, 2),
                    'generated_at' => now()->toISOString(),
                    'source' => $populationData['source'] ?? 'ai_estimation',
                    'cached' => false, // AI Assistant ma własny cache
                ]
            ];

            Log::info('✅ AI Population Estimation successful', [
                'city' => $city,
                'population' => $populationData['estimated_population'] ?? 'unknown',
                'confidence' => $populationData['confidence'] ?? 'unknown'
            ]);

            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ AI Population Estimation - validation error', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nieprawidłowe parametry żądania',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('💥 AI Population Estimation - unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas estymacji populacji',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Sprawdza status lokalnego Nominatim.
     *
     * @return JsonResponse
     */
    public function status(): JsonResponse
    {
        try {
            $localEnabled = config('app.nominatim_local_enabled', false);
            $localUrl = config('app.nominatim_local_url', 'http://localhost:8080');
            $fallbackEnabled = config('app.nominatim_fallback_enabled', true);

            $localHealth = false;
            if ($localEnabled) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)
                        ->get($localUrl . '/status');
                    $localHealth = $response->successful();
                } catch (\Exception $e) {
                    $localHealth = false;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'local_nominatim' => [
                        'enabled' => $localEnabled,
                        'url' => $localUrl,
                        'healthy' => $localHealth,
                        'status' => $localHealth ? 'online' : 'offline'
                    ],
                    'fallback' => [
                        'enabled' => $fallbackEnabled,
                        'url' => 'https://nominatim.openstreetmap.org'
                    ],
                    'active_source' => $this->getActiveSource(),
                    'configuration' => [
                        'cache_ttl' => config('app.nominatim_cache_ttl', 86400),
                        'rate_limit_delay' => config('app.nominatim_rate_limit_delay', 100)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Location status API error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Błąd podczas sprawdzania statusu',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Zwraca aktywne źródło geocodingu.
     *
     * @return string
     */
    private function getActiveSource(): string
    {
        $localEnabled = config('app.nominatim_local_enabled', false);

        if (!$localEnabled) {
            return 'external_nominatim';
        }

        // Sprawdź health lokalnego Nominatim
        try {
            $localUrl = config('app.nominatim_local_url', 'http://localhost:8080');
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get($localUrl . '/status');

            if ($response->successful()) {
                return 'local_nominatim';
            }
        } catch (\Exception $e) {
            // Local nie działa
        }

        // Fallback
        if (config('app.nominatim_fallback_enabled', true)) {
            return 'external_nominatim';
        }

        return 'none';
    }
}
