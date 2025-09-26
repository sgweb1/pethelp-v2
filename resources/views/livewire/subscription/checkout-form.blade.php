<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Potwierdzenie zamówienia</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Uzupełnij dane do faktury i potwierdź zamówienie</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formularz -->
            <div class="lg:col-span-2">
                <form wire:submit="processPayment" class="space-y-8">
                    @csrf
                    <!-- Typ faktury -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Typ faktury
                        </h2>
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="invoice_type" value="personal" class="text-blue-600">
                                <span class="ml-3 text-gray-900 dark:text-white">
                                    <strong>Osoba fizyczna</strong>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Faktura na osobę prywatną</span>
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="invoice_type" value="business" class="text-blue-600">
                                <span class="ml-3 text-gray-900 dark:text-white">
                                    <strong>Firma / Działalność gospodarcza</strong>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Faktura VAT na firmę</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Dane do faktury -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Dane do faktury
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($invoice_type === 'personal')
                                <!-- Dane osobowe -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Imię *
                                    </label>
                                    <input type="text" wire:model="first_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    @error('first_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nazwisko *
                                    </label>
                                    <input type="text" wire:model="last_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    @error('last_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            @else
                                <!-- Dane firmy -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nazwa firmy *
                                    </label>
                                    <input type="text" wire:model="company_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    @error('company_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        NIP *
                                    </label>
                                    <input type="text" wire:model="tax_id" placeholder="000-000-00-00" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    @error('tax_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <!-- Adres -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Adres *
                                </label>
                                <input type="text" wire:model="address" placeholder="ul. Przykładowa 123" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Kod pocztowy *
                                </label>
                                <input type="text" wire:model="postal_code" placeholder="00-000" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('postal_code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Miasto *
                                </label>
                                <input type="text" wire:model="city" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email do faktury *
                                </label>
                                <input type="email" wire:model="invoice_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @error('invoice_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Zgody prawne -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Zgody prawne
                        </h2>

                        <div class="space-y-4">
                            <label class="flex items-start">
                                <input type="checkbox" wire:model="accept_terms" class="mt-1 text-blue-600">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Akceptuję regulamin ogólny serwisu *</strong><br>
                                    <a href="#" class="text-blue-600 hover:text-blue-800">Przeczytaj regulamin</a>
                                </span>
                            </label>
                            @error('accept_terms') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                            <label class="flex items-start">
                                <input type="checkbox" wire:model="accept_privacy" class="mt-1 text-blue-600">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Akceptuję politykę prywatności *</strong><br>
                                    <a href="#" class="text-blue-600 hover:text-blue-800">Przeczytaj politykę prywatności</a>
                                </span>
                            </label>
                            @error('accept_privacy') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                            <label class="flex items-start">
                                <input type="checkbox" wire:model="accept_marketing" class="mt-1 text-blue-600">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Wyrażam zgodę na otrzymywanie informacji handlowych *</strong><br>
                                    <span class="text-gray-500">Zgodnie z art. 10 ust. 1 ustawy o świadczeniu usług drogą elektroniczną</span>
                                </span>
                            </label>
                            @error('accept_marketing') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                            @if($invoice_type === 'personal')
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                    <h4 class="font-medium text-blue-900 dark:text-blue-200 mb-2">
                                        Informacja dla konsumenta (art. 22¹ k.c.)
                                    </h4>
                                    <p class="text-sm text-blue-800 dark:text-blue-300 mb-3">
                                        Zgodnie z art. 27 ustawy o prawach konsumenta, konsument ma prawo do odstąpienia od umowy
                                        zawartej na odległość w terminie 14 dni od zawarcia umowy. Po tym okresie nie ma możliwości
                                        zwrotu środków za niewykorzystany okres subskrypcji.
                                    </p>
                                    <label class="flex items-start">
                                        <input type="checkbox" wire:model="consumer_withdrawal_info" class="mt-1 text-blue-600">
                                        <span class="ml-3 text-sm text-blue-800 dark:text-blue-300">
                                            Potwierdzam, że zostałem poinformowany o prawie odstąpienia od umowy
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Przyciski -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('subscription.plans') }}"
                           class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            ← Wróć do planów
                        </a>


                        <button type="submit"
                                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Przejdź do płatności</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Przetwarzanie...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Podsumowanie zamówienia -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Podsumowanie zamówienia
                    </h2>

                    <div class="space-y-4">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $plan->description }}</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Cena netto:</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($plan->price / 1.23, 2, ',', ' ') }} PLN</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">VAT (23%):</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($plan->price - ($plan->price / 1.23), 2, ',', ' ') }} PLN</span>
                            </div>
                            <div class="flex justify-between font-semibold text-lg border-t border-gray-200 dark:border-gray-700 pt-2">
                                <span class="text-gray-900 dark:text-white">Razem:</span>
                                <span class="text-gray-900 dark:text-white">{{ $plan->formatted_price }}</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Opłata za {{ $plan->billing_period === 'yearly' ? 'rok' : 'miesiąc' }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Co otrzymujesz:</h4>
                            <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                @if($plan->max_listings)
                                    <li>• {{ $plan->max_listings }} ogłoszeń</li>
                                @else
                                    <li>• Nielimitowane ogłoszenia</li>
                                @endif
                                @foreach($plan->feature_list as $feature => $label)
                                    <li>• {{ $label }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <p class="mb-1">✓ Bezpieczne płatności przez PayU</p>
                            <p class="mb-1">✓ Faktura VAT wysyłana automatycznie</p>
                            <p>✓ Możliwość anulowania w każdej chwili</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
