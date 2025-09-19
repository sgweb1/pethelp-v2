<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HasSubscriptionChecks
{
    protected function requiresFeature(string $feature, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasFeature($feature);
    }

    protected function requiresActiveSubscription(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        return $user->hasActiveSubscription();
    }

    protected function canCreateListing(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        return $user->canCreateListing();
    }

    protected function getSubscriptionError(string $feature = null): array
    {
        if ($feature) {
            return [
                'message' => 'Ta funkcja jest dostępna tylko w planach premium.',
                'feature' => $feature,
                'upgrade_url' => route('subscription.plans')
            ];
        }

        return [
            'message' => 'Wymagana jest aktywna subskrypcja.',
            'upgrade_url' => route('subscription.plans')
        ];
    }

    protected function getListingLimitError(): array
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $currentCount = $user->advertisements()->count();
        $maxListings = $subscription ? $subscription->subscriptionPlan->max_listings : 3;

        return [
            'message' => 'Osiągnięto limit ogłoszeń dla Twojego planu.',
            'current_count' => $currentCount,
            'max_listings' => $maxListings,
            'upgrade_url' => route('subscription.plans')
        ];
    }

    protected function getUserSubscriptionInfo(?User $user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return ['plan' => 'guest', 'features' => []];
        }

        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return [
                'plan' => 'basic',
                'features' => ['basic_search', 'listings', 'messaging', 'reviews', 'basic_support'],
                'max_listings' => 3,
                'current_listings' => $user->advertisements()->count()
            ];
        }

        return [
            'plan' => $subscription->subscriptionPlan->slug,
            'features' => $subscription->subscriptionPlan->features,
            'max_listings' => $subscription->subscriptionPlan->max_listings,
            'current_listings' => $user->advertisements()->count(),
            'expires_at' => $subscription->ends_at,
            'days_remaining' => $subscription->days_remaining
        ];
    }
}