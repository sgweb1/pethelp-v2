<?php

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('user can subscribe to free plan', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Basic',
        'slug' => 'basic',
        'price' => 0.00,
        'max_listings' => 3,
    ]);

    $response = $this->actingAs($user)
        ->post(route('subscription.subscribe', $plan));

    $response->assertRedirect(route('subscription.dashboard'));
    $response->assertSessionHas('success');

    expect($user->fresh())
        ->hasActiveSubscription()->toBeTrue();

    $subscription = $user->activeSubscription;
    expect($subscription)
        ->subscriptionPlan->id->toBe($plan->id)
        ->status->toBe(Subscription::STATUS_ACTIVE);
});

test('user cannot subscribe to paid plan without authentication', function () {
    $plan = SubscriptionPlan::factory()->create([
        'price' => 49.00,
    ]);

    $response = $this->post(route('subscription.subscribe', $plan));

    $response->assertRedirect(route('login'));
});

test('user can initiate paid subscription', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Pro',
        'price' => 49.00,
    ]);

    // Mock PayU service
    $this->mock(\App\Services\PayUService::class, function ($mock) {
        $mock->shouldReceive('createSubscriptionPayment')
            ->once()
            ->andReturn([
                'success' => true,
                'payment_id' => 1,
                'redirect_url' => 'https://secure.snd.payu.com/pay/12345',
                'order_id' => 'PAYU_ORDER_123',
            ]);
    });

    $response = $this->actingAs($user)
        ->post(route('subscription.subscribe', $plan));

    $response->assertRedirect('https://secure.snd.payu.com/pay/12345');
});

test('user cannot subscribe to same plan twice', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'price' => 49.00,
    ]);

    // Create existing active subscription
    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    $response = $this->actingAs($user)
        ->post(route('subscription.subscribe', $plan));

    $response->assertRedirect(route('subscription.dashboard'));
    $response->assertSessionHas('info');
});

test('payu notification creates subscription on successful payment', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'type' => 'subscription',
        'status' => 'pending',
        'amount' => $plan->price,
        'payment_data' => [
            'plan_id' => $plan->id,
            'payu_order_id' => 'PAYU_ORDER_123',
        ],
    ]);

    $notificationData = [
        'order' => [
            'orderId' => 'PAYU_ORDER_123',
            'status' => 'COMPLETED',
        ]
    ];

    // Mock PayU service
    $this->mock(\App\Services\PayUService::class, function ($mock) {
        $mock->shouldReceive('handleNotification')
            ->once()
            ->andReturn(true);
    });

    $response = $this->post(route('payu.notify'), $notificationData);

    $response->assertJson(['status' => 'OK']);
});

test('payment success page shows success message', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'payment_data' => ['payu_order_id' => 'PAYU_ORDER_123'],
    ]);

    $response = $this->actingAs($user)
        ->get(route('subscription.payment.success', ['orderId' => 'PAYU_ORDER_123']));

    $response->assertRedirect(route('subscription.dashboard'));
    $response->assertSessionHas('success');
});

test('payment cancel page shows warning message', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('subscription.payment.cancel'));

    $response->assertRedirect(route('subscription.plans'));
    $response->assertSessionHas('warning');
});

test('payment status endpoint returns payment information', function () {
    $user = User::factory()->create();
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'status' => 'completed',
        'amount' => 49.00,
        'currency' => 'PLN',
    ]);

    $response = $this->actingAs($user)
        ->get(route('subscription.payment.status', $payment));

    $response->assertJson([
        'status' => 'completed',
        'amount' => 49.00,
        'currency' => 'PLN',
    ]);
});

test('payment status endpoint denies access to other users payments', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $payment = Payment::factory()->create([
        'user_id' => $user1->id,
    ]);

    $response = $this->actingAs($user2)
        ->get(route('subscription.payment.status', $payment));

    $response->assertStatus(403);
});

test('subscription plans page displays available plans', function () {
    SubscriptionPlan::factory()->create([
        'name' => 'Basic',
        'price' => 0.00,
        'is_active' => true,
    ]);

    SubscriptionPlan::factory()->create([
        'name' => 'Pro',
        'price' => 49.00,
        'is_active' => true,
    ]);

    $response = $this->get(route('subscription.plans'));

    $response->assertStatus(200);
    $response->assertSee('Basic');
    $response->assertSee('Pro');
    $response->assertSee('49');
});

test('subscription dashboard shows current subscription', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Pro',
        'price' => 49.00,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'subscription_plan_id' => $plan->id,
        'status' => Subscription::STATUS_ACTIVE,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('subscription.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Pro');
    $response->assertSee('49');
});
