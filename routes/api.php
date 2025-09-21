<?php

use App\Http\Controllers\Api\AddressSearchController;
use App\Http\Controllers\Api\MapDataController;
use App\Http\Controllers\ApiJsLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// JavaScript error logging routes
Route::post('/js-logs', [ApiJsLogController::class, 'store'])
    ->middleware(['throttle:60,1']); // Max 60 requests per minute

Route::get('/js-logs', [ApiJsLogController::class, 'index'])
    ->middleware(['auth']); // Tylko zalogowani mogą pobierać logi

// Address search API
Route::get('/search-addresses', [AddressSearchController::class, 'search'])
    ->middleware(['throttle:120,1']); // Max 120 requests per minute

// Map API routes with performance optimization
Route::prefix('map')->middleware('map.throttle')->group(function () {
    Route::get('/items', [MapDataController::class, 'getMapItems'])->name('api.map.items');
    Route::get('/clusters', [MapDataController::class, 'getClusterData'])->name('api.map.clusters');
    Route::get('/statistics', [MapDataController::class, 'getStatistics'])->name('api.map.statistics');

    // Cache management - can be protected with auth/admin middleware
    Route::delete('/cache', [MapDataController::class, 'clearCache'])->name('api.map.clear-cache');
});
