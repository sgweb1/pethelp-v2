<?php

namespace App\Livewire\Subscription;

use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use App\Traits\HasSubscriptionChecks;
use Livewire\Component;

class PricingPage extends Component
{
    use HasSubscriptionChecks;

    public string $billingPeriod = 'monthly';

    /**
     * Oblicza rzeczywistą cenę planu z uwzględnieniem proration.
     */
    public function calculatePlanPrice(SubscriptionPlan $plan): array
    {
        $user = auth()->user();
        if (! $user || ! $user->activeSubscription) {
            return [
                'original_price' => $plan->price,
                'final_price' => $plan->price,
                'credit_amount' => 0,
                'savings' => 0,
                'has_proration' => false,
            ];
        }

        $subscriptionService = app(SubscriptionService::class);
        $prorationData = $subscriptionService->calculateProration($user, $plan);

        return [
            'original_price' => $plan->price,
            'final_price' => $prorationData['amount_to_charge'],
            'credit_amount' => $prorationData['credit_amount'] ?? 0,
            'savings' => $plan->price - $prorationData['amount_to_charge'],
            'has_proration' => $prorationData['is_plan_change'] ?? false,
            'proration_data' => $prorationData,
        ];
    }

    public function render()
    {
        $allPlans = SubscriptionPlan::active()->ordered()->get();

        // Grupujemy plany według nazwy (każda nazwa ma wersję miesięczną i roczną)
        $planGroups = $allPlans->groupBy('name');

        // Tworzymy główne plany z informacją o wersjach miesięcznych/rocznych
        $plans = $planGroups->map(function ($group, $name) {
            $monthlyPlan = $group->where('billing_period', 'monthly')->first();
            $yearlyPlan = $group->where('billing_period', 'yearly')->first();

            // Użyjemy planu z wybranego okresu rozliczeniowego jako główny
            $mainPlan = $this->billingPeriod === 'yearly' ? $yearlyPlan : $monthlyPlan;

            // Dodamy informacje o drugim planie do obliczania oszczędności
            if ($mainPlan) {
                $mainPlan->monthlyVariant = $monthlyPlan;
                $mainPlan->yearlyVariant = $yearlyPlan;

                // Dodaj informacje o proration dla tego planu
                $mainPlan->pricing = $this->calculatePlanPrice($mainPlan);
            }

            return $mainPlan;
        })->filter()->values();

        $currentSubscription = auth()->user()?->activeSubscription;
        $userInfo = $this->getUserSubscriptionInfo();

        return view('livewire.subscription.pricing-page', [
            'plans' => $plans,
            'allPlans' => $allPlans,
            'currentSubscription' => $currentSubscription,
            'userInfo' => $userInfo,
            'billingPeriod' => $this->billingPeriod,
        ])->layout('components.dashboard-layout');
    }

    public function setBillingPeriod($period)
    {
        $this->billingPeriod = $period;
    }
}
