<?php

namespace App\Livewire\Subscription;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\InFaktService;
use App\Traits\HasSubscriptionChecks;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    public string $statusFilter = 'all';
    use HasSubscriptionChecks, WithPagination;

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;
        $userInfo = $this->getUserSubscriptionInfo();

        $recentPayments = Payment::where(function ($query) use ($user) {
                // Płatności związane z booking (pet sitter services)
                $query->whereHas('booking', function ($bookingQuery) use ($user) {
                    $bookingQuery->where('owner_id', $user->id)
                                 ->orWhere('sitter_id', $user->id);
                });
            })
            ->orWhere(function ($query) use ($user) {
                // Płatności subskrypcyjne (bez booking)
                $query->whereNull('booking_id')
                      ->whereJsonContains('gateway_response->user_id', $user->id);
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['booking.owner', 'booking.sitter', 'booking.service'])
            ->latest()
            ->paginate(10);

        $availablePlans = SubscriptionPlan::active()->ordered()->get();

        $breadcrumbs = [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('dashboard')
            ],
            [
                'title' => 'Subskrypcje',
                'icon' => '💳'
            ]
        ];

        return view('livewire.subscription.dashboard', [
            'subscription' => $subscription,
            'userInfo' => $userInfo,
            'recentPayments' => $recentPayments,
            'availablePlans' => $availablePlans,
        ])->layout('components.dashboard-layout', compact('breadcrumbs'));
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

    /**
     * Regeneruje fakturę dla płatności subskrypcyjnej.
     *
     * @param int $paymentId
     * @return void
     */
    public function regenerateInvoice(int $paymentId)
    {
        $user = auth()->user();

        // Znajdź płatność należącą do użytkownika
        $payment = Payment::whereNull('booking_id') // Tylko płatności subskrypcyjne
                         ->whereJsonContains('gateway_response->user_id', $user->id)
                         ->where('id', $paymentId)
                         ->where('status', 'completed')
                         ->first();

        if (!$payment) {
            session()->flash('error', 'Nie można odnaleźć płatności lub brak dostępu.');
            return;
        }

        try {
            // Pobierz plan subskrypcji - najpierw po slug, potem po ID
            $planSlug = $payment->gateway_response['plan_slug'] ?? null;
            $planId = $payment->gateway_response['plan_id'] ?? null;

            if ($planSlug) {
                $plan = SubscriptionPlan::where('slug', $planSlug)->first();
            } elseif ($planId) {
                $plan = SubscriptionPlan::find($planId);
            } else {
                session()->flash('error', 'Brak informacji o planie w płatności.');
                return;
            }

            if (!$plan) {
                session()->flash('error', 'Plan subskrypcji nie został znaleziony.');
                return;
            }

            // Regeneruj fakturę przez InFakt
            $inFaktService = app(InFaktService::class);
            $result = $inFaktService->createInvoiceForSubscription($payment, $user, $plan);

            if ($result['success']) {
                session()->flash('success', 'Faktura została wygenerowana pomyślnie.');
            } else {
                $errorMessage = $result['error'] ?? 'Nieznany błąd';
                // Jeśli error jest tablicą, przekonwertuj na string
                if (is_array($errorMessage)) {
                    $errorMessage = json_encode($errorMessage, JSON_UNESCAPED_UNICODE);
                }
                session()->flash('error', 'Nie udało się wygenerować faktury: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            \Log::error('Błąd regeneracji faktury', [
                'payment_id' => $paymentId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', 'Wystąpił błąd podczas generowania faktury.');
        }
    }
}