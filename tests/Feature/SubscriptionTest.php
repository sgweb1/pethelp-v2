<?php

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('user can have subscription', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Pro',
        'price' => 49.00,
        'features' => ['advanced_search', 'unlimited_listings']
    ]);

    $subscription = Subscription::createFromPlan($user, $plan);

    expect($subscription)
        ->user_id->toBe($user->id)
        ->subscription_plan_id->toBe($plan->id);

    $this->assertDatabaseHas('subscriptions', [
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
    ]);
});

test('subscription is active when within date range', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    expect($subscription)
        ->isActive()->toBeTrue()
        ->isExpired()->toBeFalse();
});

test('subscription is expired when end date passed', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subMonths(2),
        'ends_at' => now()->subDay(),
    ]);

    expect($subscription)
        ->isActive()->toBeFalse()
        ->isExpired()->toBeTrue();
});

test('subscription can be cancelled', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    expect($subscription->canBeCancelled())->toBeTrue();

    $subscription->cancel();

    expect($subscription)
        ->status->toBe(Subscription::STATUS_CANCELLED)
        ->cancelled_at->not->toBeNull()
        ->isCancelled()->toBeTrue();
});

test('subscription can be resumed', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_CANCELLED,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
        'cancelled_at' => now(),
    ]);

    expect($subscription->canBeResumed())->toBeTrue();

    $subscription->resume();

    expect($subscription)
        ->status->toBe(Subscription::STATUS_ACTIVE)
        ->cancelled_at->toBeNull();
});

test('subscription has feature access', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'features' => ['advanced_search', 'unlimited_listings', 'analytics']
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    expect($subscription)
        ->hasFeature('advanced_search')->toBeTrue()
        ->hasFeature('unlimited_listings')->toBeTrue()
        ->hasFeature('api_access')->toBeFalse();
});

test('user has feature through active subscription', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'features' => ['advanced_search', 'analytics']
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    expect($user)
        ->hasFeature('advanced_search')->toBeTrue()
        ->hasFeature('analytics')->toBeTrue()
        ->hasFeature('api_access')->toBeFalse();
});

test('subscription renewal works correctly', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'billing_period' => 'monthly'
    ]);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subMonth(),
        'ends_at' => now()->addDay(),
        'next_billing_at' => now()->addDay(),
    ]);

    $originalEndDate = $subscription->ends_at;
    $originalNextBilling = $subscription->next_billing_at;

    $subscription->renew();

    expect($subscription->ends_at->greaterThan($originalEndDate))->toBeTrue();
    expect($subscription->next_billing_at->greaterThan($originalNextBilling))->toBeTrue();
    expect($subscription->last_payment_at)->not->toBeNull();
});
