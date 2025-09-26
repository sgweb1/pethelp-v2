<?php

namespace App\Livewire\Subscription;

use App\Models\SubscriptionPlan;
use App\Traits\HasSubscriptionChecks;
use Livewire\Component;

class PricingPage extends Component
{
    use HasSubscriptionChecks;

    public string $billingPeriod = 'monthly';

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
            'billingPeriod' => $this->billingPeriod
        ])->layout('components.dashboard-layout');
    }

    public function setBillingPeriod($period)
    {
        $this->billingPeriod = $period;
    }
}
