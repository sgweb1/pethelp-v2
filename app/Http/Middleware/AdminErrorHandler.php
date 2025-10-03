<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware izolujący błędy w panelu administracyjnym.
 *
 * Przechwytuje wszystkie wyjątki występujące w panelu admina
 * i wyświetla przyjazną stronę błędu zamiast crashować całą aplikację.
 * Dzięki temu błędy w adminie nie wpływają na dostępność głównej aplikacji.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class AdminErrorHandler
{
    /**
     * Obsługuje żądanie i przechwytuje błędy panelu admina.
     *
     * @param  Request  $request  Żądanie HTTP
     * @param  Closure  $next  Następny middleware
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            // Loguj błąd do pliku z pełnym stack trace
            Log::error('Admin Panel Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            // Jeśli to żądanie AJAX/JSON (np. Livewire)
            if ($request->expectsJson() || $request->is('*/livewire/*')) {
                return response()->json([
                    'message' => 'Wystąpił błąd w panelu administracyjnym. Spróbuj ponownie lub skontaktuj się z administratorem.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null,
                ], 500);
            }

            // Dla zwykłych żądań HTTP - wyświetl dedykowaną stronę błędu admina
            return response()->view('errors.admin', [
                'exception' => $e,
                'message' => $e->getMessage(),
                'showDebug' => config('app.debug'),
            ], 500);
        }
    }
}
