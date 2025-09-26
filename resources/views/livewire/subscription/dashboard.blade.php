<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Zarządzanie subskrypcją</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Sprawdź status swojej subskrypcji i zarządzaj płatnościami</p>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                    <div class="flex">
                        <x-icon name="heroicon-s-check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        <p class="ml-3 text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <div class="flex">
                        <x-icon name="heroicon-s-x-circle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                        <p class="ml-3 text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Current Subscription -->
                <div class="lg:col-span-2">
                    <!-- Subscription Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Aktualny plan</h2>

                        @if($subscription)
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscription->subscriptionPlan->name }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $subscription->subscriptionPlan->description }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ $subscription->status_label }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Cena</h4>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $subscription->formatted_price }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">/{{ $subscription->billing_period === 'yearly' ? 'rok' : 'miesiąc' }}</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Ważna do</h4>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $subscription->ends_at->format('d.m.Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->days_remaining }} dni pozostało</p>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Następna płatność</h4>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $subscription->next_billing_at ? $subscription->next_billing_at->format('d.m.Y') : 'Brak' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $subscription->next_billing_amount ? $subscription->formatted_price : 'Plan darmowy' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Subscription Actions -->
                            <div class="flex flex-wrap gap-3">
                                @if($subscription->canBeCancelled())
                                    <button wire:click="cancelSubscription"
                                            wire:confirm="Czy na pewno chcesz anulować subskrypcję?"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                        Anuluj subskrypcję
                                    </button>
                                @endif

                                @if($subscription->canBeResumed())
                                    <button wire:click="resumeSubscription"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                        Wznów subskrypcję
                                    </button>
                                @endif

                                <a href="{{ route('subscription.plans') }}"
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    Zmień plan
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-icon name="heroicon-o-star" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Brak aktywnej subskrypcji</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Wybierz plan, aby odblokować funkcje premium</p>
                                <a href="{{ route('subscription.plans') }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    Zobacz plany
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Historia płatności</h2>

                        @if($recentPayments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Opis</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kwota</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faktura</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($recentPayments as $payment)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $payment->created_at->format('d.m.Y H:i') }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">
                                                    {{ $payment->description }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ number_format($payment->amount, 2, ',', ' ') }} {{ $payment->currency }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                           ($payment->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                                           'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                    @if($payment->status === 'completed' && $payment->amount > 0 && !$payment->booking_id)
                                                        @php
                                                            $invoiceId = $payment->gateway_response['infakt_invoice_id'] ?? null;
                                                            $invoiceNumber = $payment->gateway_response['infakt_invoice_number'] ?? null;
                                                        @endphp

                                                        @if($invoiceId)
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('subscription.invoices.download', $payment) }}"
                                                                   class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-800 hover:bg-blue-200 dark:hover:bg-blue-700 transition-colors duration-200"
                                                                   target="_blank">
                                                                    📄 {{ $invoiceNumber ? "#{$invoiceNumber}" : 'Pobierz PDF' }}
                                                                </a>
                                                            </div>
                                                        @else
                                                            <button wire:click="regenerateInvoice({{ $payment->id }})"
                                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-800 hover:bg-orange-200 dark:hover:bg-orange-700 transition-colors duration-200">
                                                                🔄 Generuj fakturę
                                                            </button>
                                                        @endif
                                                    @elseif($payment->amount == 0)
                                                        <span class="text-gray-500 text-xs">Plan darmowy</span>
                                                    @elseif($payment->status !== 'completed')
                                                        <span class="text-gray-500 text-xs">-</span>
                                                    @else
                                                        <span class="text-gray-500 text-xs">Brak faktury</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $recentPayments->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-icon name="heroicon-o-document-text" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                                <p class="text-gray-600 dark:text-gray-400">Brak historii płatności</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Usage Stats -->
                    @if($subscription)
                        <x-subscription.usage-meter
                            :current="$userInfo['current_listings']"
                            :limit="$userInfo['max_listings']"
                            label="Ogłoszenia"
                            class="mb-6"
                        />
                    @endif

                    <!-- Features List -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Twoje funkcje</h3>
                        <div class="space-y-3">
                            @foreach($userInfo['features'] as $feature)
                                <div class="flex items-center">
                                    <x-icon name="heroicon-s-check" class="w-5 h-5 text-green-500 mr-3" />
                                    <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Upgrade Prompt -->
                    @if(!$subscription || $subscription->subscriptionPlan->slug === 'basic')
                        <x-subscription.upgrade-prompt
                            title="Odblokuj więcej funkcji"
                            description="Przejdź na plan premium, aby uzyskać dostęp do zaawansowanych funkcji."
                            :show-plans="false"
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>