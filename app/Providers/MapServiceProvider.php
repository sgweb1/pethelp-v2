<?php

namespace App\Providers;

use App\Models\MapItem;
use App\Observers\MapItemObserver;
use App\Services\MapCacheService;
use Illuminate\Support\ServiceProvider;

class MapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register MapCacheService as singleton
        $this->app->singleton(MapCacheService::class, function ($app) {
            return new MapCacheService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register model observers
        MapItem::observe(MapItemObserver::class);

        // Register cache invalidation for other models that sync to MapItem
        $this->registerModelObservers();
    }

    private function registerModelObservers(): void
    {
        // Advertisement model observer
        if (class_exists(\App\Models\Advertisement::class)) {
            \App\Models\Advertisement::updated(function ($advertisement) {
                $this->invalidateCacheForModel($advertisement);
            });

            \App\Models\Advertisement::deleted(function ($advertisement) {
                $this->invalidateCacheForModel($advertisement);
            });
        }

        // Event model observer
        if (class_exists(\App\Models\Event::class)) {
            \App\Models\Event::updated(function ($event) {
                $this->invalidateCacheForModel($event);
            });

            \App\Models\Event::deleted(function ($event) {
                $this->invalidateCacheForModel($event);
            });
        }

        // ProfessionalService model observer
        if (class_exists(\App\Models\ProfessionalService::class)) {
            \App\Models\ProfessionalService::updated(function ($service) {
                $this->invalidateCacheForModel($service);
            });

            \App\Models\ProfessionalService::deleted(function ($service) {
                $this->invalidateCacheForModel($service);
            });
        }
    }

    private function invalidateCacheForModel($model): void
    {
        try {
            // Check if model has map location trait
            if (method_exists($model, 'mapItem')) {
                $cacheService = app(MapCacheService::class);
                $cacheService->invalidateMapCache();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning(
                "Failed to invalidate cache for model: " . get_class($model),
                ['error' => $e->getMessage()]
            );
        }
    }
}