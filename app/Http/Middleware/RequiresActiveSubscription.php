<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class RequiresActiveSubscription
{
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Wymagana jest aktywna subskrypcja.',
                    'upgrade_url' => route('subscription.plans')
                ], 403);
            }

            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Wymagana jest aktywna subskrypcja, aby uzyskać dostęp do tej funkcji.')
                ->with('action', 'upgrade');
        }

        return $next($request);
    }
}