<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class CheckListingLimits
{
    public function handle(Request $request, Closure $next): BaseResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->canCreateListing()) {
            $subscription = $user->activeSubscription;
            $currentCount = $user->advertisements()->count();
            $maxListings = $subscription ? $subscription->subscriptionPlan->max_listings : 3;

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Osiągnięto limit ogłoszeń dla Twojego planu.',
                    'current_count' => $currentCount,
                    'max_listings' => $maxListings,
                    'upgrade_url' => route('subscription.plans')
                ], 403);
            }

            return redirect()
                ->back()
                ->with('error', "Osiągnięto limit ogłoszeń ({$currentCount}/{$maxListings}). Zaktualizuj plan, aby dodać więcej ogłoszeń.")
                ->with('required_feature', 'unlimited_listings');
        }

        return $next($request);
    }
}