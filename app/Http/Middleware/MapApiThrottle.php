<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class MapApiThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->getRateLimitKey($request);

        // Different limits for different endpoints
        $limits = $this->getLimitsForEndpoint($request);

        foreach ($limits as $limit => $decay) {
            $limiterKey = $key . ':' . $limit;

            if (RateLimiter::tooManyAttempts($limiterKey, $limit)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                    'retry_after' => RateLimiter::availableIn($limiterKey)
                ], 429);
            }

            RateLimiter::increment($limiterKey, $decay);
        }

        return $next($request);
    }

    private function getRateLimitKey(Request $request): string
    {
        $identifier = $request->ip();

        // Use user ID if authenticated for more accurate limiting
        if ($request->user()) {
            $identifier = 'user:' . $request->user()->id;
        }

        return 'map_api:' . $identifier;
    }

    private function getLimitsForEndpoint(Request $request): array
    {
        $route = $request->route()->getName();

        return match($route) {
            'api.map.items' => [
                120 => 60,    // 120 requests per minute
                600 => 3600   // 600 requests per hour
            ],
            'api.map.clusters' => [
                60 => 60,     // 60 requests per minute
                300 => 3600   // 300 requests per hour
            ],
            'api.map.statistics' => [
                30 => 60,     // 30 requests per minute
                120 => 3600   // 120 requests per hour
            ],
            'api.map.clear-cache' => [
                5 => 60,      // 5 requests per minute
                10 => 3600    // 10 requests per hour
            ],
            default => [
                60 => 60,     // Default: 60 requests per minute
                300 => 3600   // 300 requests per hour
            ]
        };
    }
}