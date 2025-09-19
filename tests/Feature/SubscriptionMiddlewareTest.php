<?php

use App\Http\Middleware\RequiresFeature;
use App\Http\Middleware\RequiresActiveSubscription;
use App\Http\Middleware\CheckListingLimits;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('requires feature middleware allows access with feature', function () {
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

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $user);

    $middleware = new RequiresFeature();
    $response = $middleware->handle($request, fn() => new Response('success'), 'advanced_search');

    expect($response->getContent())->toBe('success');
});

test('requires feature middleware redirects without feature', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'features' => ['basic_search'] // No advanced_search
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $user);

    $middleware = new RequiresFeature();
    $response = $middleware->handle($request, fn() => new Response('success'), 'advanced_search');

    expect($response->getStatusCode())->toBe(302);
});

test('requires feature middleware returns json for api requests', function () {
    $user = User::factory()->create();

    $request = Request::create('/api/test', 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(fn() => $user);

    $middleware = new RequiresFeature();
    $response = $middleware->handle($request, fn() => new Response('success'), 'advanced_search');

    expect($response->getStatusCode())->toBe(403);

    $content = json_decode($response->getContent(), true);
    expect($content)
        ->toHaveKey('message')
        ->toHaveKey('feature', 'advanced_search');
});

test('requires active subscription middleware allows access with active subscription', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $user);

    $middleware = new RequiresActiveSubscription();
    $response = $middleware->handle($request, fn() => new Response('success'));

    expect($response->getContent())->toBe('success');
});

test('requires active subscription middleware redirects without subscription', function () {
    $user = User::factory()->create();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn() => $user);

    $middleware = new RequiresActiveSubscription();
    $response = $middleware->handle($request, fn() => new Response('success'));

    expect($response->getStatusCode())->toBe(302);
});

test('check listing limits middleware allows creation within limits', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'max_listings' => 5
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    // Create 2 existing listings (under limit of 5)
    \App\Models\Advertisement::factory(2)->create(['user_id' => $user->id]);

    $request = Request::create('/test', 'POST');
    $request->setUserResolver(fn() => $user);

    $middleware = new CheckListingLimits();
    $response = $middleware->handle($request, fn() => new Response('success'));

    expect($response->getContent())->toBe('success');
});

test('check listing limits middleware blocks creation at limit', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'max_listings' => 3
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    // Create 3 existing listings (at limit)
    \App\Models\Advertisement::factory(3)->create(['user_id' => $user->id]);

    $request = Request::create('/test', 'POST');
    $request->setUserResolver(fn() => $user);

    $middleware = new CheckListingLimits();
    $response = $middleware->handle($request, fn() => new Response('success'));

    expect($response->getStatusCode())->toBe(302);
});

test('check listing limits middleware allows unlimited for premium plans', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'max_listings' => null // unlimited
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    // Create 100 existing listings (way above typical limits)
    \App\Models\Advertisement::factory(100)->create(['user_id' => $user->id]);

    $request = Request::create('/test', 'POST');
    $request->setUserResolver(fn() => $user);

    $middleware = new CheckListingLimits();
    $response = $middleware->handle($request, fn() => new Response('success'));

    expect($response->getContent())->toBe('success');
});
