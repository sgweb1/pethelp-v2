<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ograniczający dostęp tylko do środowiska lokalnego.
 *
 * Blokuje dostęp do określonych route'ów w środowiskach produkcyjnych
 * dla zwiększenia bezpieczeństwa.
 *
 * @package App\Http\Middleware
 * @author Claude AI Assistant
 */
class LocalOnlyMiddleware
{
    /**
     * Obsługuje żądanie HTTP.
     *
     * Sprawdza czy aplikacja działa w środowisku lokalnym.
     * Jeśli nie - zwraca błąd 404.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') !== 'local') {
            abort(404, 'This feature is only available in local environment');
        }

        return $next($request);
    }
}