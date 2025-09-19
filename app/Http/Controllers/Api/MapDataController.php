<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MapCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MapDataController extends Controller
{
    public function __construct(
        private MapCacheService $cacheService
    ) {}

    public function getMapItems(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bounds' => 'array|size:4',
            'bounds.*' => 'numeric',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'content_types' => 'nullable|array',
            'content_types.*' => 'string|in:service,event,adoption,lost_pet,found_pet,supplies',
            'search_term' => 'nullable|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'zoom_level' => 'nullable|integer|min:1|max:18',
            'city' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $filters = $validator->validated();
        $limit = $filters['limit'] ?? 100;
        unset($filters['limit']);

        // Clean empty values
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '' && (!is_array($value) || !empty($value));
        });

        try {
            $items = $this->cacheService->getCachedMapItems($filters, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'count' => $items->count(),
                    'cache_hit' => true // Simplified - in real implementation track this
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve map data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getClusterData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bounds' => 'required|array|size:4',
            'bounds.*' => 'numeric',
            'zoom_level' => 'required|integer|min:1|max:18',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $bounds = $request->input('bounds');
        $zoomLevel = $request->input('zoom_level');

        try {
            $clusterData = $this->cacheService->getCachedClusterData($bounds, $zoomLevel);

            return response()->json([
                'success' => true,
                'data' => $clusterData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cluster data',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getStatistics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bounds' => 'array|size:4',
            'bounds.*' => 'numeric',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
            'content_types' => 'nullable|array',
            'content_types.*' => 'string|in:service,event,adoption,lost_pet,found_pet,supplies',
            'search_term' => 'nullable|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'zoom_level' => 'nullable|integer|min:1|max:18',
            'city' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $filters = $validator->validated();

        // Clean empty values
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '' && (!is_array($value) || !empty($value));
        });

        try {
            $stats = $this->cacheService->getCachedStatistics($filters);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function clearCache(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pattern' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pattern = $request->input('pattern');
            $this->cacheService->invalidateMapCache($pattern);

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}