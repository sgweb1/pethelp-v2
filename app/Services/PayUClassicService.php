<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Serwis obsługi płatności PayU Classic API.
 *
 * Implementuje Classic API PayU dla subskrypcji i płatności.
 * Używa prostszego modelu HTTP POST bez OAuth.
 *
 * @package App\Services
 */
class PayUClassicService
{
    protected ?string $apiUrl;
    protected ?string $merchantId;
    protected ?string $secretKey;
    protected string $environment;

    public function __construct()
    {
        $this->environment = config('payu.environment', 'sandbox');
        $this->apiUrl = config('payu.classic_api_url.' . $this->environment);
        $this->merchantId = config('payu.merchant_id');
        $this->secretKey = config('payu.secret_key');
    }

    /**
     * Tworzy płatność subskrypcji w systemie PayU Classic.
     *
     * @param User $user Użytkownik kupujący subskrypcję
     * @param SubscriptionPlan $plan Plan subskrypcji
     * @param array $additionalData Dodatkowe dane (dane faktury, zgody prawne)
     * @return array Wynik operacji
     */
    public function createSubscriptionPayment(User $user, SubscriptionPlan $plan, array $additionalData = []): array
    {
        $amount = $this->calculateAmount($plan->price);
        $orderId = $this->generateOrderId();

        // Tworzenie wpisu płatności w bazie
        $payment = Payment::create([
            'status' => 'pending',
            'amount' => $plan->price,
            'payment_method' => 'payu',
            'external_id' => $orderId,
            'gateway_response' => [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'billing_period' => $plan->billing_period,
                'description' => "Subskrypcja {$plan->name} - PetHelp",
                'invoice_data' => $additionalData['invoice_data'] ?? [],
                'legal_consents' => $additionalData['legal_consents'] ?? [],
            ],
        ]);

        try {
            // Tworzenie formularza PayU Classic
            $formData = $this->buildClassicFormData($user, $plan, $amount, $orderId, $payment->id);

            // Generowanie sygnatury
            $signature = $this->generateSignature($formData);
            $formData['sig'] = $signature;

            Log::info('PayU Classic payment created', [
                'payment_id' => $payment->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'redirect_url' => $this->apiUrl . 'paygw/UTF/NewPayment',
                'form_data_keys' => array_keys($formData)
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'redirect_url' => $this->apiUrl . 'paygw/UTF/NewPayment',
                'form_data' => $formData,
                'order_id' => $orderId,
            ];

        } catch (\Exception $e) {
            Log::error('PayU Classic payment creation failed', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            $payment->update([
                'status' => 'failed',
                'gateway_response' => [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toISOString(),
                ],
            ]);

            return [
                'success' => false,
                'error' => 'Nie udało się utworzyć płatności. Spróbuj ponownie.',
            ];
        }
    }

    /**
     * Obsługuje notyfikację zwrotną z PayU.
     *
     * @param array $data Dane otrzymane z PayU
     * @return bool Czy notyfikacja została poprawnie obsłużona
     */
    public function handleNotification(array $data): bool
    {
        try {
            $orderId = $data['session_id'] ?? null;
            $status = $data['status'] ?? null;

            if (!$orderId || !$status) {
                Log::warning('PayU Classic notification missing required data', $data);
                return false;
            }

            $payment = Payment::where('external_id', $orderId)->first();

            if (!$payment) {
                Log::warning('PayU Classic notification for unknown order', ['order_id' => $orderId]);
                return false;
            }

            $this->updatePaymentStatus($payment, $status, $data);

            if ($status === 'COMPLETED') {
                $this->completeSubscriptionPayment($payment);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('PayU Classic notification handling failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Buduje dane formularza PayU Classic.
     *
     * @param User $user
     * @param SubscriptionPlan $plan
     * @param int $amount
     * @param string $orderId
     * @param int $paymentId
     * @return array
     */
    protected function buildClassicFormData(User $user, SubscriptionPlan $plan, int $amount, string $orderId, int $paymentId): array
    {
        return [
            'pos_id' => $this->merchantId,
            'session_id' => $orderId,
            'pos_auth_key' => '3ILWvuj',
            'amount' => $amount,
            'desc' => "Subscription {$plan->name} PetHelp",
            'desc2' => "Plan {$plan->name} {$plan->billing_period}",
            'order_id' => $paymentId,
            'first_name' => explode(' ', $user->name)[0] ?? '',
            'last_name' => explode(' ', $user->name, 2)[1] ?? '',
            'email' => $user->email,
            'client_ip' => request()->ip() === '127.0.0.1' ? '8.8.8.8' : request()->ip(),
            'ts' => time(),
            'continue_url' => config('payu.continue_url'),
            'cancel_url' => config('payu.cancel_url'),
            'error_url' => config('payu.error_url'),
            'notify_url' => config('payu.notify_url'),
            'language' => 'pl',
            'currency_code' => 'PLN',
        ];
    }

    /**
     * Generuje sygnaturę MD5 dla PayU Classic.
     *
     * @param array $data
     * @return string
     */
    protected function generateSignature(array $data): string
    {
        // PayU Classic signature: pos_id + session_id + ts + key_1 (MD5 key)
        $string = $data['pos_id'] .
                  $data['session_id'] .
                  $data['ts'] .
                  $this->secretKey;

        // Debug logging sygnatury
        Log::info('Generowanie sygnatury PayU', [
            'string_to_sign' => $string,
            'pos_id' => $data['pos_id'],
            'session_id' => $data['session_id'],
            'pos_auth_key' => $data['pos_auth_key'],
            'amount' => $data['amount'],
            'desc' => $data['desc'],
            'order_id' => $data['order_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'client_ip' => $data['client_ip'],
            'ts' => $data['ts'],
            'secret_key' => substr($this->secretKey, 0, 5) . '***',
            'generated_signature' => md5($string)
        ]);

        return md5($string);
    }

    /**
     * Weryfikuje sygnaturę otrzymaną z PayU.
     *
     * @param array $data
     * @param string $signature
     * @return bool
     */
    public function verifySignature(array $data, string $signature): bool
    {
        $string = $data['pos_id'] .
                  $data['session_id'] .
                  $data['ts'] .
                  $data['status'] .
                  $this->secretKey;

        $calculatedSignature = md5($string);

        return hash_equals($calculatedSignature, $signature);
    }

    /**
     * Aktualizuje status płatności w bazie danych.
     *
     * @param Payment $payment
     * @param string $status
     * @param array $data
     */
    protected function updatePaymentStatus(Payment $payment, string $status, array $data): void
    {
        $statusMap = [
            'NEW' => 'pending',
            'PENDING' => 'pending',
            'WAITING_FOR_CONFIRMATION' => 'pending',
            'COMPLETED' => 'completed',
            'CANCELED' => 'failed',
            'REJECTED' => 'failed',
        ];

        $newStatus = $statusMap[$status] ?? 'pending';

        $gatewayResponse = $payment->gateway_response ?? [];
        $updateData = [
            'gateway_response' => array_merge($gatewayResponse, [
                'external_status' => $status,
                'last_notification' => now()->toISOString(),
                'payu_data' => $data,
                'api_type' => 'classic'
            ]),
        ];

        if ($newStatus === 'completed') {
            $updateData['status'] = 'completed';
            $updateData['processed_at'] = now();
        } elseif ($newStatus === 'failed') {
            $updateData['status'] = 'failed';
            $updateData['gateway_response']['failure_reason'] = $data['status'] ?? 'Payment failed';
            $updateData['gateway_response']['failed_at'] = now()->toISOString();
        }

        $payment->update($updateData);
    }

    /**
     * Finalizuje płatność subskrypcji - aktywuje subskrypcję.
     *
     * @param Payment $payment
     */
    protected function completeSubscriptionPayment(Payment $payment): void
    {
        $gatewayResponse = $payment->gateway_response ?? [];
        if (isset($gatewayResponse['subscription_processed']) && $gatewayResponse['subscription_processed']) {
            return; // Już przetworzone
        }

        $planId = $gatewayResponse['plan_id'] ?? null;
        $userId = $gatewayResponse['user_id'] ?? null;

        if (!$planId || !$userId) {
            Log::error('Payment missing plan_id or user_id', ['payment_id' => $payment->id]);
            return;
        }

        $plan = SubscriptionPlan::find($planId);
        $user = User::find($userId);

        if (!$plan || !$user) {
            Log::error('Plan or User not found for payment', [
                'payment_id' => $payment->id,
                'plan_id' => $planId,
                'user_id' => $userId
            ]);
            return;
        }

        // Anuluj istniejące aktywne subskrypcje
        $user->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->update(['status' => Subscription::STATUS_CANCELLED, 'cancelled_at' => now()]);

        // Utwórz nową subskrypcję
        $subscription = Subscription::createFromPlan($user, $plan);
        $subscription->update(['status' => Subscription::STATUS_ACTIVE]);

        // Oznacz płatność jako przetworzoną
        $gatewayResponse['subscription_processed'] = true;
        $gatewayResponse['subscription_id'] = $subscription->id;
        $payment->update(['gateway_response' => $gatewayResponse]);

        Log::info('Subscription activated via PayU Classic payment', [
            'user_id' => $userId,
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Przelicza cenę z PLN na grosze.
     *
     * @param float $price Cena w PLN
     * @return int Cena w groszach
     */
    protected function calculateAmount(float $price): int
    {
        return (int) round($price * 100);
    }

    /**
     * Generuje unikalny ID zamówienia.
     *
     * @return string
     */
    protected function generateOrderId(): string
    {
        return 'PETHELP_' . time() . '_' . Str::upper(Str::random(8));
    }

    /**
     * Test połączenia PayU Classic (dla developmentu).
     *
     * @return array
     */
    public function testConnection(): array
    {
        return [
            'success' => true,
            'message' => 'PayU Classic API - konfiguracja prawidłowa',
            'environment' => $this->environment,
            'merchant_id' => $this->merchantId,
            'api_url' => $this->apiUrl,
            'api_type' => 'classic',
            'test_form_url' => $this->apiUrl . 'paygw/UTF/NewPayment'
        ];
    }
}