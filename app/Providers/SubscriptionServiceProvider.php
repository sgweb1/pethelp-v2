<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerGates();
    }

    protected function registerBladeDirectives(): void
    {
        Blade::if('hasFeature', function (string $feature) {
            return auth()->check() && auth()->user()->hasFeature($feature);
        });

        Blade::if('hasActiveSubscription', function () {
            return auth()->check() && auth()->user()->hasActiveSubscription();
        });

        Blade::if('canCreateListing', function () {
            return auth()->check() && auth()->user()->canCreateListing();
        });

        Blade::if('subscriptionPlan', function (string $planSlug) {
            $user = auth()->user();
            return $user && $user->activeSubscription && $user->activeSubscription->subscriptionPlan->slug === $planSlug;
        });
    }

    protected function registerGates(): void
    {
        Gate::define('create-listing', function (User $user) {
            return $user->canCreateListing();
        });

        Gate::define('access-feature', function (User $user, string $feature) {
            return $user->hasFeature($feature);
        });

        Gate::define('access-advanced-search', function (User $user) {
            return $user->hasFeature('advanced_search');
        });

        Gate::define('access-analytics', function (User $user) {
            return $user->hasFeature('analytics');
        });

        Gate::define('access-promoted-listings', function (User $user) {
            return $user->hasFeature('promoted_listings');
        });

        Gate::define('access-ai-matching', function (User $user) {
            return $user->hasFeature('ai_matching');
        });

        Gate::define('access-priority-support', function (User $user) {
            return $user->hasFeature('priority_support');
        });

        Gate::define('access-api', function (User $user) {
            return $user->hasFeature('api_access');
        });
    }
}
