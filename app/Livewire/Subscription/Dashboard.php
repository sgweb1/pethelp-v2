<?php

namespace App\Livewire\Subscription;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Traits\HasSubscriptionChecks;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use HasSubscriptionChecks, WithPagination;

    public function render()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;
        $userInfo = $this->getUserSubscriptionInfo();

        $recentPayments = Payment::where('user_id', $user->id)
            ->with('subscription.subscriptionPlan')
            ->latest()
            ->paginate(10);

        $availablePlans = SubscriptionPlan::active()->ordered()->get();

        return view('livewire.subscription.dashboard', [
            'subscription' => $subscription,
            'userInfo' => $userInfo,
            'recentPayments' => $recentPayments,
            'availablePlans' => $availablePlans,
        ])->layout('components.dashboard-layout');
    }

    public function cancelSubscription()
    {
        $subscription = auth()->user()->activeSubscription;

        if (!$subscription || !$subscription->canBeCancelled()) {
            session()->flash('error', 'Nie można anulować tej subskrypcji.');
            return;
        }

        $subscription->cancel();

        session()->flash('success', 'Subskrypcja została anulowana. Zachowasz dostęp do funkcji premium do ' . $subscription->ends_at->format('d.m.Y') . '.');
    }

    public function resumeSubscription()
    {
        $subscription = auth()->user()->activeSubscription;

        if (!$subscription || !$subscription->canBeResumed()) {
            session()->flash('error', 'Nie można wznowić tej subskrypcji.');
            return;
        }

        $subscription->resume();

        session()->flash('success', 'Subskrypcja została wznowiona.');
    }
}