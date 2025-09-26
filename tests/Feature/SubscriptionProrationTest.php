<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * Testy systemu rozliczenia proporcjonalnego (proration) subskrypcji.
 *
 * Testuje różne scenariusze:
 * - Upgrade z Basic na Pro z kredytem za niewykorzystany czas
 * - Downgrade z Business na Pro
 * - Przedłużenie tego samego planu
 * - Natychmiastowa aktywacja nowego planu
 *
 * @package Tests\Feature
 * @author Claude AI Assistant
 */
class SubscriptionProrationTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionService $subscriptionService;
    private User $user;
    private SubscriptionPlan $basicPlan;
    private SubscriptionPlan $proPlan;
    private SubscriptionPlan $businessPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriptionService = app(SubscriptionService::class);

        // Utwórz użytkownika testowego
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);

        // Utwórz plany subskrypcji
        $this->basicPlan = SubscriptionPlan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Plan podstawowy',
            'price' => 0.00,
            'billing_period' => 'monthly',
            'max_listings' => 3,
            'features' => ['basic_search', 'listings', 'messaging'],
            'is_active' => true,
            'sort_order' => 1
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'Plan profesjonalny',
            'price' => 49.00,
            'billing_period' => 'monthly',
            'max_listings' => null,
            'features' => ['basic_search', 'listings', 'messaging', 'unlimited_listings'],
            'is_active' => true,
            'sort_order' => 2
        ]);

        $this->businessPlan = SubscriptionPlan::create([
            'name' => 'Business',
            'slug' => 'business',
            'description' => 'Plan biznesowy',
            'price' => 199.00,
            'billing_period' => 'monthly',
            'max_listings' => null,
            'features' => ['basic_search', 'listings', 'messaging', 'unlimited_listings', 'api_access'],
            'is_active' => true,
            'sort_order' => 3
        ]);
    }

    /**
     * Test scenariusza: Nowy użytkownik bez subskrypcji kupuje plan Pro.
     */
    public function test_new_user_subscription_no_proration(): void
    {
        // Sprawdź proration dla nowego użytkownika
        $prorationData = $this->subscriptionService->calculateProration($this->user, $this->proPlan);

        $this->assertFalse($prorationData['is_plan_change']);
        $this->assertEquals(0, $prorationData['credit_amount']);
        $this->assertEquals(49.00, $prorationData['amount_to_charge']);
        $this->assertNull($prorationData['current_plan']);
        $this->assertEquals('pro', $prorationData['new_plan']);
    }

    /**
     * Test scenariusza: Użytkownik z planem Pro przedłuża subskrypcję.
     */
    public function test_extend_same_plan(): void
    {
        // Stwórz aktywną subskrypcję Pro
        $currentSubscription = $this->createActiveSubscription($this->user, $this->proPlan, now(), now()->addMonth());

        // Oblicz proration dla przedłużenia tego samego planu
        $prorationData = $this->subscriptionService->calculateProration($this->user, $this->proPlan);

        $this->assertFalse($prorationData['is_plan_change']);
        $this->assertTrue($prorationData['period_extension']);
        $this->assertEquals(0, $prorationData['credit_amount']);
        $this->assertEquals(49.00, $prorationData['amount_to_charge']);
        $this->assertEquals('pro', $prorationData['current_plan']);
        $this->assertEquals('pro', $prorationData['new_plan']);
    }

    /**
     * Test scenariusza upgrade z proporcjonalnym rozliczeniem.
     *
     * Użytkownik ma plan Pro (49 PLN/miesiąc) ważny przez 15 dni.
     * Chce upgrade na Business (199 PLN/miesiąc).
     * Powinien otrzymać kredyt za niewykorzystane 15 dni.
     */
    public function test_upgrade_with_proration(): void
    {
        // Stwórz subskrypcję Pro ważną przez kolejne 15 dni
        $startDate = now()->subDays(15);
        $endDate = now()->addDays(15);
        $currentSubscription = $this->createActiveSubscription($this->user, $this->proPlan, $startDate, $endDate);

        // Oblicz proration dla upgrade na Business
        $prorationData = $this->subscriptionService->calculateProration($this->user, $this->businessPlan);

        $this->assertTrue($prorationData['is_plan_change']);
        $this->assertEquals('pro', $prorationData['current_plan']);
        $this->assertEquals('business', $prorationData['new_plan']);
        $this->assertEquals(15, $prorationData['days_remaining']);

        // Sprawdź obliczenie kredytu (15 dni z 30 dni = 50% z 49 PLN = 24.50 PLN)
        $expectedCredit = 49.00 * (15 / 30);
        $this->assertEquals(round($expectedCredit, 2), $prorationData['credit_amount']);

        // Sprawdź kwotę do zapłaty (199 - 24.50 = 174.50)
        $expectedCharge = 199.00 - $expectedCredit;
        $this->assertEquals(round($expectedCharge, 2), $prorationData['amount_to_charge']);
    }

    /**
     * Test scenariusza downgrade z rozliczeniem.
     *
     * Użytkownik ma plan Business (199 PLN/miesiąc) ważny przez 20 dni.
     * Chce downgrade na Pro (49 PLN/miesiąc).
     * Powinien otrzymać kredyt i płacić minimalną kwotę lub 0.
     */
    public function test_downgrade_with_proration(): void
    {
        // Stwórz subskrypcję Business ważną przez kolejne 20 dni
        $startDate = now()->subDays(10);
        $endDate = now()->addDays(20);
        $currentSubscription = $this->createActiveSubscription($this->user, $this->businessPlan, $startDate, $endDate);

        // Oblicz proration dla downgrade na Pro
        $prorationData = $this->subscriptionService->calculateProration($this->user, $this->proPlan);

        $this->assertTrue($prorationData['is_plan_change']);
        $this->assertEquals('business', $prorationData['current_plan']);
        $this->assertEquals('pro', $prorationData['new_plan']);
        $this->assertEquals(20, $prorationData['days_remaining']);

        // Sprawdź obliczenie kredytu (20 dni z 30 dni z 199 PLN)
        $expectedCredit = 199.00 * (20 / 30);
        $this->assertEquals(round($expectedCredit, 2), $prorationData['credit_amount']);

        // Sprawdź kwotę do zapłaty (49 - 132.67 = 0, minimum 0)
        $expectedCharge = max(0, 49.00 - $expectedCredit);
        $this->assertEquals($expectedCharge, $prorationData['amount_to_charge']);
    }

    /**
     * Test integracji: pełen proces zmiany planu.
     */
    public function test_full_subscription_change_process(): void
    {
        // 1. Utwórz aktywną subskrypcję Pro
        $currentSubscription = $this->createActiveSubscription($this->user, $this->proPlan, now()->subDays(10), now()->addDays(20));

        // 2. Utwórz płatność za upgrade na Business
        $payment = $this->subscriptionService->createSubscriptionPayment($this->user, $this->businessPlan, 'test');

        $this->assertEquals('pending', $payment->status);
        $this->assertTrue($payment->isSubscriptionPayment());
        $this->assertEquals($this->user->id, $payment->user_id);
        $this->assertEquals($this->businessPlan->id, $payment->subscription_plan_id);

        // Sprawdź czy proration został zastosowany
        $this->assertGreaterThan(0, $payment->proration_credit);
        $this->assertLessThan($this->businessPlan->price, $payment->amount);

        // 3. Przetwórz płatność
        $success = $this->subscriptionService->processSubscriptionPayment($payment);

        $this->assertTrue($success);
        $this->assertEquals('completed', $payment->fresh()->status);

        // 4. Sprawdź czy stara subskrypcja została anulowana
        $this->assertEquals(Subscription::STATUS_CANCELLED, $currentSubscription->fresh()->status);

        // 5. Sprawdź czy nowa subskrypcja została utworzona
        $newSubscription = $this->user->fresh()->activeSubscription;
        $this->assertNotNull($newSubscription);
        $this->assertEquals($this->businessPlan->id, $newSubscription->subscription_plan_id);
        $this->assertEquals(Subscription::STATUS_ACTIVE, $newSubscription->status);
    }

    /**
     * Test przedłużenia istniejącej subskrypcji.
     */
    public function test_extend_existing_subscription(): void
    {
        // 1. Utwórz aktywną subskrypcję Pro kończącą się za tydzień
        $currentEndDate = now()->addWeek();
        $currentSubscription = $this->createActiveSubscription($this->user, $this->proPlan, now()->subMonth(), $currentEndDate);

        // 2. Utwórz płatność za przedłużenie tego samego planu
        $payment = $this->subscriptionService->createSubscriptionPayment($this->user, $this->proPlan, 'test');

        // 3. Przetwórz płatność
        $success = $this->subscriptionService->processSubscriptionPayment($payment);

        $this->assertTrue($success);

        // 4. Sprawdź czy subskrypcja została przedłużona
        $extendedSubscription = $currentSubscription->fresh();
        $this->assertEquals(Subscription::STATUS_ACTIVE, $extendedSubscription->status);

        // Data zakończenia powinna zostać przedłużona o miesiąc
        $expectedNewEndDate = $currentEndDate->copy()->addMonth();
        $this->assertEquals(
            $expectedNewEndDate->format('Y-m-d H:i'),
            $extendedSubscription->ends_at->format('Y-m-d H:i')
        );
    }

    /**
     * Test pobrania szczegółów proration dla UI.
     */
    public function test_get_proration_details_for_ui(): void
    {
        // Utwórz aktywną subskrypcję
        $currentSubscription = $this->createActiveSubscription($this->user, $this->proPlan, now()->subDays(10), now()->addDays(20));

        // Pobierz szczegóły dla UI
        $details = $this->subscriptionService->getProrationDetails($this->user, $this->businessPlan);

        $this->assertArrayHasKey('current_subscription', $details);
        $this->assertArrayHasKey('new_plan', $details);
        $this->assertArrayHasKey('proration', $details);
        $this->assertArrayHasKey('summary', $details);

        $this->assertEquals('Pro', $details['current_subscription']['plan_name']);
        $this->assertEquals('Business', $details['new_plan']['name']);
        $this->assertTrue($details['proration']['is_plan_change']);
        $this->assertGreaterThan(0, $details['proration']['credit_amount']);
        $this->assertTrue($details['summary']['immediate_activation']);
    }

    /**
     * Pomocnicza metoda do tworzenia aktywnej subskrypcji.
     */
    private function createActiveSubscription(User $user, SubscriptionPlan $plan, Carbon $startDate, Carbon $endDate): Subscription
    {
        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'price' => $plan->price,
            'billing_period' => $plan->billing_period,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'next_billing_at' => $endDate->copy(),
            'last_payment_at' => $startDate,
            'metadata' => [
                'test_subscription' => true
            ]
        ]);
    }
}