<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class RequiresFeature
{
    public function handle(Request $request, Closure $next, string $feature): BaseResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ta funkcja jest dostępna tylko w planach premium.',
                    'feature' => $feature,
                    'upgrade_url' => route('subscription.plans')
                ], 403);
            }

            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Ta funkcja jest dostępna tylko w planach premium.')
                ->with('required_feature', $feature);
        }

        return $next($request);
    }
}