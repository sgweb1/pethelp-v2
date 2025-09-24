<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        private LocationSearchService $locationService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);

        $query = $request->input('q');
        $limit = $request->input('limit', 10);

        $results = $this->locationService->searchHierarchical($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'query' => $query,
                'count' => count($results),
                'limit' => $limit,
            ],
        ]);
    }

    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        $lat = (float) $request->input('lat');
        $lon = (float) $request->input('lon');

        $result = $this->locationService->getLocationDetails($lat, $lon);

        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Location not found',
        ], 404);
    }
}
