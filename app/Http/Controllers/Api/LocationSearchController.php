<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationSearchController extends Controller
{
    public function __construct(
        private LocationSearchService $locationService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query', '');

        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
            ]);
        }

        try {
            $suggestions = $this->locationService->searchHierarchical($query, 10);

            return response()->json([
                'suggestions' => $suggestions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'suggestions' => [],
                'error' => 'Błąd wyszukiwania lokalizacji',
            ], 500);
        }
    }

    public function coordinates(Request $request): JsonResponse
    {
        $query = $request->get('query', '');

        if (empty($query)) {
            return response()->json([
                'coordinates' => null,
                'error' => 'Brak zapytania',
            ], 400);
        }

        try {
            $coordinates = $this->locationService->getCoordinates($query);

            return response()->json([
                'coordinates' => $coordinates,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'coordinates' => null,
                'error' => 'Nie udało się pobrać współrzędnych',
            ], 500);
        }
    }
}
