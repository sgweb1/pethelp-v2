<?php

namespace App\Livewire\Subscription;

use App\Models\SubscriptionPlan;
use App\Traits\HasSubscriptionChecks;
use Livewire\Component;

class PricingPage extends Component
{
    use HasSubscriptionChecks;

    public function render()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        $currentSubscription = auth()->user()?->activeSubscription;
        $userInfo = $this->getUserSubscriptionInfo();

        return view('livewire.subscription.pricing-page', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'userInfo' => $userInfo
        ])->layout('layouts.app');
    }
}
