<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayUService
{
    protected string $apiUrl;
    protected string $merchantId;
    protected string $secretKey;
    protected string $oauthClientId;
    protected string $oauthClientSecret;
    protected string $environment;

    public function __construct()
    {
        $this->environment = config('payu.environment');
        $this->apiUrl = config('payu.api_url.' . $this->environment);
        $this->merchantId = config('payu.merchant_id');
        $this->secretKey = config('payu.secret_key');
        $this->oauthClientId = config('payu.oauth_client_id');
        $this->oauthClientSecret = config('payu.oauth_client_secret');
    }

    public function createSubscriptionPayment(User $user, SubscriptionPlan $plan): array
    {
        $amount = $this->calculateAmount($plan->price);
        $orderId = $this->generateOrderId();

        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => null, // Will be set after subscription creation
            'type' => 'subscription',
            'status' => 'pending',
            'amount' => $plan->price,
            'currency' => config('payu.currency'),
            'payment_method' => 'payu',
            'external_id' => $orderId,
            'description' => "Subskrypcja {$plan->name} - PetHelp",
            'payment_data' => [
                'plan_id' => $plan->id,
                'billing_period' => $plan->billing_period,
            ],
        ]);

        $orderData = $this->buildOrderData($user, $plan, $amount, $orderId);

        try {
            $token = $this->getOAuthToken();
            $response = $this->createOrder($orderData, $token);

            if (isset($response['redirectUri'])) {
                $payment->update([
                    'external_status' => $response['status']['statusCode'] ?? 'PENDING',
                    'payment_data' => array_merge($payment->payment_data, [
                        'payu_order_id' => $response['orderId'] ?? null,
                        'redirect_uri' => $response['redirectUri'],
                    ]),
                ]);

                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'redirect_url' => $response['redirectUri'],
                    'order_id' => $response['orderId'] ?? null,
                ];
            }

            throw new \Exception('PayU did not return redirect URI');

        } catch (\Exception $e) {
            Log::error('PayU payment creation failed', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Nie udało się utworzyć płatności. Spróbuj ponownie.',
            ];
        }
    }

    public function handleNotification(array $data): bool
    {
        try {
            $orderId = $data['order']['orderId'] ?? null;
            $status = $data['order']['status'] ?? null;

            if (!$orderId || !$status) {
                Log::warning('PayU notification missing required data', $data);
                return false;
            }

            $payment = Payment::where('payment_data->payu_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('PayU notification for unknown order', ['order_id' => $orderId]);
                return false;
            }

            $this->updatePaymentStatus($payment, $status, $data);

            if ($status === 'COMPLETED') {
                $this->completeSubscriptionPayment($payment);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('PayU notification handling failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function getOAuthToken(): string
    {
        $response = Http::asForm()->post($this->apiUrl . 'pl/standard/user/oauth/authorize', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->oauthClientId,
            'client_secret' => $this->oauthClientSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayU OAuth token: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new \Exception('PayU OAuth response missing access token');
        }

        return $data['access_token'];
    }

    protected function createOrder(array $orderData, string $token): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . 'api/v2_1/orders', $orderData);

        if (!$response->successful()) {
            throw new \Exception('PayU order creation failed: ' . $response->body());
        }

        return $response->json();
    }

    protected function buildOrderData(User $user, SubscriptionPlan $plan, int $amount, string $orderId): array
    {
        return [
            'notifyUrl' => config('payu.notify_url'),
            'continueUrl' => config('payu.continue_url'),
            'customerIp' => request()->ip(),
            'merchantPosId' => $this->merchantId,
            'description' => "Subskrypcja {$plan->name} - PetHelp",
            'currencyCode' => config('payu.currency'),
            'totalAmount' => $amount,
            'extOrderId' => $orderId,
            'buyer' => [
                'email' => $user->email,
                'firstName' => explode(' ', $user->name)[0] ?? '',
                'lastName' => explode(' ', $user->name, 2)[1] ?? '',
                'language' => config('payu.locale'),
            ],
            'products' => [
                [
                    'name' => "Plan {$plan->name}",
                    'unitPrice' => $amount,
                    'quantity' => 1,
                ],
            ],
            'payMethods' => [
                'payMethod' => [
                    'type' => config('payu.default_payment_method'),
                ],
            ],
        ];
    }

    protected function calculateAmount(float $price): int
    {
        return (int) round($price * 100); // PayU expects amount in grosze
    }

    protected function generateOrderId(): string
    {
        return 'PETHELP_' . time() . '_' . Str::upper(Str::random(8));
    }

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

        $updateData = [
            'external_status' => $status,
            'payment_data' => array_merge($payment->payment_data, [
                'last_notification' => now()->toISOString(),
                'payu_data' => $data,
            ]),
        ];

        if ($newStatus === 'completed') {
            $updateData['status'] = 'completed';
            $updateData['paid_at'] = now();
        } elseif ($newStatus === 'failed') {
            $updateData['status'] = 'failed';
            $updateData['failed_at'] = now();
            $updateData['failure_reason'] = $data['order']['status'] ?? 'Payment failed';
        }

        $payment->update($updateData);
    }

    protected function completeSubscriptionPayment(Payment $payment): void
    {
        if ($payment->subscription_id) {
            return; // Already processed
        }

        $planId = $payment->payment_data['plan_id'] ?? null;
        if (!$planId) {
            Log::error('Payment missing plan_id', ['payment_id' => $payment->id]);
            return;
        }

        $plan = SubscriptionPlan::find($planId);
        if (!$plan) {
            Log::error('Plan not found for payment', ['payment_id' => $payment->id, 'plan_id' => $planId]);
            return;
        }

        // Cancel existing active subscriptions
        $payment->user->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->update(['status' => Subscription::STATUS_CANCELLED, 'cancelled_at' => now()]);

        // Create new subscription
        $subscription = Subscription::createFromPlan($payment->user, $plan);
        $subscription->update(['status' => Subscription::STATUS_ACTIVE]);

        // Link payment to subscription
        $payment->update(['subscription_id' => $subscription->id]);

        Log::info('Subscription activated via PayU payment', [
            'user_id' => $payment->user_id,
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
        ]);
    }

    public function verifySignature(array $data, string $signature): bool
    {
        // PayU signature verification implementation
        $string = implode('|', [
            $data['merchantId'] ?? '',
            $data['posId'] ?? '',
            $data['sessionId'] ?? '',
            $data['amount'] ?? '',
            $data['currency'] ?? '',
            $this->secretKey,
        ]);

        $calculatedSignature = hash(config('payu.signature_algorithm'), $string);

        return hash_equals($calculatedSignature, $signature);
    }
}