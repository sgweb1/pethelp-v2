<?php

use App\Http\Controllers\Api\AddressSearchController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MapDataController;
use App\Http\Controllers\Api\TrelloWebhookController;
use App\Http\Controllers\Api\UnifiedSearchController;
use App\Http\Controllers\ApiJsLogController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// PayU webhook endpoint (no CSRF required)
Route::post('/payu/notify', [PaymentController::class, 'payuNotification'])
    ->name('api.payu.notify');

// InFakt webhook endpoint (no CSRF required)
Route::post('/infakt/webhook', [\App\Http\Controllers\InvoiceController::class, 'inFaktWebhook'])
    ->name('api.infakt.webhook');

// JavaScript error logging routes
Route::post('/js-logs', [ApiJsLogController::class, 'store'])
    ->middleware(['throttle:60,1']); // Max 60 requests per minute

Route::get('/js-logs', [ApiJsLogController::class, 'index'])
    ->middleware(['auth']); // Tylko zalogowani mogą pobierać logi

// Address search API
Route::get('/search-addresses', [AddressSearchController::class, 'search'])
    ->middleware(['throttle:120,1']); // Max 120 requests per minute

// Hierarchical location search API
Route::prefix('locations')->middleware('throttle:120,1')->group(function () {
    Route::get('/search', [LocationController::class, 'search'])->name('api.locations.search');
    Route::get('/reverse', [LocationController::class, 'reverseGeocode'])->name('api.locations.reverse');
});

// 🚀 UNIFIED SEARCH API - Single endpoint for all search needs (replaces map/* endpoints)
Route::prefix('search')->middleware('throttle:200,1')->group(function () {
    Route::get('/', [UnifiedSearchController::class, 'search'])->name('api.unified-search');
    Route::get('/stats', [UnifiedSearchController::class, 'stats'])->name('api.search.stats');
});

// 🔗 Trello Webhook Integration
Route::prefix('trello')->group(function () {
    Route::post('/webhook', [TrelloWebhookController::class, 'handleWebhook'])->name('api.trello.webhook');
    Route::get('/webhook/verify', [TrelloWebhookController::class, 'verifyWebhook'])->name('api.trello.webhook.verify');
});

// 📌 LEGACY: Map API routes (DEPRECATED - use /api/search instead)
// Keeping for backward compatibility, will be removed in future version
Route::prefix('map')->middleware('map.throttle')->group(function () {
    Route::get('/items', [MapDataController::class, 'getMapItems'])->name('api.map.items');
    Route::get('/clusters', [MapDataController::class, 'getClusterData'])->name('api.map.clusters');
    Route::get('/statistics', [MapDataController::class, 'getStatistics'])->name('api.map.statistics');

    // Cache management - can be protected with auth/admin middleware
    Route::delete('/cache', [MapDataController::class, 'clearCache'])->name('api.map.clear-cache');
});
