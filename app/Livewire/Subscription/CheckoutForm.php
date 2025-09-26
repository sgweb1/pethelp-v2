<?php

namespace App\Livewire\Subscription;

use App\Models\SubscriptionPlan;
use App\Services\PayUService;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CheckoutForm extends Component
{
    public SubscriptionPlan $plan;

    // Dane do faktury - osoba fizyczna
    #[Validate('required_if:invoice_type,personal|string|max:100')]
    public string $first_name = '';

    #[Validate('required_if:invoice_type,personal|string|max:100')]
    public string $last_name = '';

    // Dane do faktury - firma
    #[Validate('required_if:invoice_type,business|string|max:200')]
    public string $company_name = '';

    #[Validate('required_if:invoice_type,business|string|max:15')]
    public string $tax_id = '';

    // Adres
    #[Validate('required|string|max:200')]
    public string $address = '';

    #[Validate('required|string|max:10')]
    public string $postal_code = '';

    #[Validate('required|string|max:100')]
    public string $city = '';

    #[Validate('required|string|max:100')]
    public string $country = 'Polska';

    // Email do faktury (może być inny niż konto)
    #[Validate('required|email|max:255')]
    public string $invoice_email = '';

    // Typ faktury
    #[Validate('required|in:personal,business')]
    public string $invoice_type = 'personal';

    // Zgody prawne
    #[Validate('accepted')]
    public bool $accept_terms = false;

    #[Validate('accepted')]
    public bool $accept_privacy = false;

    #[Validate('accepted')]
    public bool $accept_marketing = false;

    // Zgoda konsumenta (tylko dla osób fizycznych)
    public bool $consumer_withdrawal_info = false;

    public function mount(SubscriptionPlan $plan)
    {
        \Log::info('=== CheckoutForm MOUNT START ===');
        \Log::info('Current time: ' . now());
        \Log::info('Plan ID: ' . $plan->id);

        $this->plan = $plan;

        // Wypełnij dane użytkownika
        $user = auth()->user();
        if ($user) {
            $this->invoice_email = $user->email;

            // Jeśli użytkownik ma profil z danymi
            if ($user->profile) {
                $this->first_name = $user->name ? explode(' ', $user->name)[0] : '';
                $this->last_name = $user->name ? (explode(' ', $user->name, 2)[1] ?? '') : '';
            }
        }
    }

    /**
     * Breadcrumbs dla checkout.
     *
     * @return array
     */
    public function getBreadcrumbsProperty(): array
    {
        return [
            [
                'title' => 'Panel',
                'icon' => '🏠',
                'url' => route('dashboard')
            ],
            [
                'title' => 'Plany subskrypcji',
                'icon' => '💳',
                'url' => route('subscription.plans')
            ],
            [
                'title' => 'Potwierdzenie zamówienia',
                'icon' => '📋'
            ]
        ];
    }

    public function processPayment()
    {
        \Log::info('=== CheckoutForm processPayment START ===');
        \Log::info('Current time: ' . now());
        \Log::info('User ID: ' . auth()->user()->id);
        \Log::info('Plan ID: ' . $this->plan->id);

        $this->validate();

        \Log::info('CheckoutForm validation passed');

        try {
            \Log::info('Creating PayU REST service');

            // Używamy nowoczesnego PayU REST API
            $payuService = app(\App\Services\PayURestService::class);

            \Log::info('Calling createSubscriptionPayment', [
                'user_id' => auth()->user()->id,
                'plan_id' => $this->plan->id
            ]);

            $result = $payuService->createSubscriptionPayment(auth()->user(), $this->plan, [
                'invoice_data' => [
                    'type' => $this->invoice_type,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'company_name' => $this->company_name,
                    'tax_id' => $this->tax_id,
                    'address' => $this->address,
                    'postal_code' => $this->postal_code,
                    'city' => $this->city,
                    'country' => $this->country,
                    'invoice_email' => $this->invoice_email,
                ],
                'legal_consents' => [
                    'terms' => $this->accept_terms,
                    'privacy' => $this->accept_privacy,
                    'marketing' => $this->accept_marketing,
                    'consumer_info' => $this->consumer_withdrawal_info,
                ]
            ]);

            if ($result['success']) {
                // PayU REST API - bezpośrednie przekierowanie do PayU
                return redirect($result['redirect_url']);
            }

            session()->flash('error', $result['error']);
        } catch (\Exception $e) {
            \Log::error('CheckoutForm processPayment exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Wystąpił błąd podczas przetwarzania płatności. Spróbuj ponownie.');
        }
    }


    public function render()
    {
        $breadcrumbs = $this->breadcrumbs;

        return view('livewire.subscription.checkout-form', compact('breadcrumbs'))
            ->layout('components.dashboard-layout');
    }
}
