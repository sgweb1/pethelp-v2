<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware przekierowujący użytkowników nie będących administratorami.
 *
 * Sprawdza czy zalogowany użytkownik ma rolę administratora.
 * Jeśli nie - przekierowuje na /profile. Jeśli nie jest zalogowany - pozwala
 * na dostęp do strony logowania Filament.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class RedirectNonAdminToProfile
{
    /**
     * Obsługuje przychodzące żądanie.
     *
     * Sprawdza uprawnienia użytkownika i przekierowuje non-adminów.
     *
     * @param  Request  $request  Żądanie HTTP
     * @param  Closure  $next  Następny middleware w łańcuchu
     * @return Response Odpowiedź HTTP
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jeśli użytkownik jest zalogowany
        if (Auth::check()) {
            $user = Auth::user();

            // Sprawdź czy to żądanie do panelu admina (nie do API logowania)
            $isAdminPanel = $request->is('admin') || $request->is('admin/*');
            $isLoginRequest = $request->is('admin/login');

            // Jeśli to panel admina (nie strona logowania) i użytkownik nie jest adminem
            if ($isAdminPanel && ! $isLoginRequest && ! $user->isAdmin()) {
                return redirect('/profile')->with('error', 'Nie masz dostępu do panelu administracyjnego.');
            }
        }

        return $next($request);
    }
}
