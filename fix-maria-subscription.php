<?php

/**
 * Skrypt do ręcznego utworzenia subskrypcji dla płatności Marii.
 *
 * Uruchom: php fix-maria-subscription.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

echo "🔧 Skrypt naprawy subskrypcji Marii\n";
echo "===================================\n\n";

// Znajdź płatność Marii (ID 76 - completed)
$payment = Payment::find(76);

if (!$payment) {
    echo "❌ Płatność ID 76 nie została znaleziona!\n";
    exit(1);
}

echo "✅ Znaleziono płatność ID: {$payment->id}\n";
echo "   Status: {$payment->status}\n";
echo "   Kwota: {$payment->amount} PLN\n";
echo "   Data: {$payment->created_at}\n\n";

// Sprawdź dane w gateway_response
$gatewayResponse = $payment->gateway_response ?? [];
$planId = $gatewayResponse['plan_id'] ?? null;
$userId = $gatewayResponse['user_id'] ?? null;

// Jeśli plan_id to stare ID 2, zaktualizuj na nowe ID 15 (Pro monthly) i dodaj slug
if ($planId == 2) {
    echo "⚠️  Wykryto stare plan_id=2, aktualizuję na plan_id=15 i plan_slug=pro-monthly\n";
    $planId = 15;
    $gatewayResponse['plan_id'] = 15;
    $gatewayResponse['plan_slug'] = 'pro-monthly';
    $payment->update(['gateway_response' => $gatewayResponse]);
}

echo "📊 Dane z gateway_response:\n";
echo "   Plan ID: " . ($planId ?: 'BRAK') . "\n";
echo "   Plan Slug: " . ($gatewayResponse['plan_slug'] ?? 'BRAK') . "\n";
echo "   User ID: " . ($userId ?: 'BRAK') . "\n";
echo "   Subscription processed: " . (($gatewayResponse['subscription_processed'] ?? false) ? 'TAK' : 'NIE') . "\n\n";

if (!$planId || !$userId) {
    echo "❌ Brakuje plan_id lub user_id w gateway_response!\n";
    exit(1);
}

// Znajdź plan i użytkownika
$plan = SubscriptionPlan::find($planId);
$user = User::find($userId);

if (!$plan) {
    echo "❌ Plan ID {$planId} nie został znaleziony!\n";
    exit(1);
}

if (!$user) {
    echo "❌ User ID {$userId} nie został znaleziony!\n";
    exit(1);
}

echo "✅ Znaleziono plan: {$plan->name} ({$plan->price} PLN, {$plan->billing_period})\n";
echo "✅ Znaleziono użytkownika: {$user->name} ({$user->email})\n\n";

// Sprawdź czy już ma subskrypcję
$existingSubscription = $user->activeSubscription;
if ($existingSubscription) {
    echo "⚠️  Użytkownik ma już aktywną subskrypcję:\n";
    echo "   Plan: {$existingSubscription->subscriptionPlan->name}\n";
    echo "   Status: {$existingSubscription->status}\n";
    echo "   Kończy się: {$existingSubscription->ends_at}\n";

    $response = readline("Czy chcesz ją anulować i utworzyć nową? (y/N): ");
    if (strtolower($response) !== 'y') {
        echo "Anulowano.\n";
        exit(0);
    }

    // Anuluj istniejącą subskrypcję
    $user->subscriptions()
        ->where('status', Subscription::STATUS_ACTIVE)
        ->update(['status' => Subscription::STATUS_CANCELLED, 'cancelled_at' => now()]);

    echo "✅ Anulowano istniejące subskrypcje\n";
}

echo "🔨 Tworzenie nowej subskrypcji...\n";

try {
    // Utwórz nową subskrypcję
    $subscription = Subscription::createFromPlan($user, $plan);
    $subscription->update(['status' => Subscription::STATUS_ACTIVE]);

    echo "✅ Utworzono subskrypcję ID: {$subscription->id}\n";
    echo "   Plan: {$subscription->subscriptionPlan->name}\n";
    echo "   Status: {$subscription->status}\n";
    echo "   Rozpoczyna się: {$subscription->starts_at}\n";
    echo "   Kończy się: {$subscription->ends_at}\n\n";

    // Oznacz płatność jako przetworzoną
    $gatewayResponse['subscription_processed'] = true;
    $gatewayResponse['subscription_id'] = $subscription->id;
    $payment->update(['gateway_response' => $gatewayResponse]);

    echo "✅ Zaktualizowano płatność - oznaczono jako przetworzoną\n\n";

    echo "🎉 SUKCES!\n";
    echo "Maria ma teraz aktywną subskrypcję planu {$plan->name}!\n";

} catch (\Exception $e) {
    echo "❌ Błąd podczas tworzenia subskrypcji:\n";
    echo "   {$e->getMessage()}\n";
    echo "   Plik: {$e->getFile()}:{$e->getLine()}\n";
    exit(1);
}