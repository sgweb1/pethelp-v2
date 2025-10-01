<div class="max-w-4xl mx-auto p-6">
    <!-- Booking Summary -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Podsumowanie rezerwacji</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <span class="text-2xl">{{ $booking->service->category->icon ?? '🐾' }}</span>
                    <div>
                        <h3 class="font-semibold text-lg">{{ $booking->service->title }}</h3>
                        <p class="text-gray-600">{{ $booking->service->category->name }}</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Opiekun:</span> {{ $booking->sitter->name }}</div>
                    <div><span class="font-medium">Zwierzę:</span> {{ $booking->pet->name }} ({{ $booking->pet->type_label }})</div>
                    <div><span class="font-medium">Okres:</span> {{ $booking->start_date->format('d.m.Y H:i') }} - {{ $booking->end_date->format('d.m.Y H:i') }}</div>
                    <div><span class="font-medium">Czas trwania:</span> {{ $booking->duration_in_hours }} godzin</div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-3">Szczegóły płatności</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Cena usługi:</span>
                        <span>{{ number_format($booking->total_price, 2) }} zł</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Prowizja platformy (10%):</span>
                        <span>{{ number_format($this->commissionAmount, 2) }} zł</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Opiekun otrzyma:</span>
                        <span>{{ number_format($this->sitterAmount, 2) }} zł</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Do zapłaty:</span>
                        <span>{{ number_format($booking->total_price, 2) }} zł</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Process -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Płatność</h2>

        @if($step === 'select_method')
            <!-- Payment Method Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Wybierz metodę płatności</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($this->paymentMethods as $method => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   wire:model.live="paymentMethod"
                                   value="{{ $method }}"
                                   class="sr-only">
                            <div class="border-2 rounded-lg p-4 text-center transition-colors
                                {{ $paymentMethod === $method ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400' }}">
                                <div class="text-2xl mb-2">
                                    @if($method === 'card') 💳
                                    @elseif($method === 'blik') 📱
                                    @elseif($method === 'transfer') 🏦
                                    @endif
                                </div>
                                <div class="font-medium">{{ $label }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Payment Button -->
            <div class="text-center">
                <button wire:click="processPayment"
                        wire:loading.attr="disabled"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold text-lg disabled:opacity-50">
                    <span wire:loading.remove>Zapłać {{ number_format($booking->total_price, 2) }} zł</span>
                    <span wire:loading>Przetwarzanie...</span>
                </button>
            </div>

        @elseif($step === 'processing')
            <!-- Processing State -->
            <div class="text-center py-12">
                <div class="animate-spin w-16 h-16 border-4 border-indigo-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Przetwarzanie płatności...</h3>
                <p class="text-gray-600">Proszę czekać, nie zamykaj tej strony</p>
            </div>

        @elseif($step === 'success')
            <!-- Success State -->
            <div class="text-center py-12">
                <div class="text-6xl text-green-600 mb-4">✅</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Płatność zakończona pomyślnie!</h3>
                <p class="text-gray-600 mb-6">Twoja rezerwacja została potwierdzona</p>

                @if($payment)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 max-w-md mx-auto mb-6">
                        <div class="text-sm text-green-800">
                            <div><strong>ID transakcji:</strong> {{ $payment->external_id }}</div>
                            <div><strong>Metoda płatności:</strong> {{ $payment->payment_method_label }}</div>
                            <div><strong>Data:</strong> {{ $payment->processed_at?->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                @endif

                <div class="space-x-4">
                    <a href="{{ route('profile.bookings') }}"
                       class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                        Przejdź do rezerwacji
                    </a>
                    <a href="{{ route('profile.dashboard') }}"
                       class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                        Powrót do panelu
                    </a>
                </div>
            </div>

        @elseif($step === 'failed')
            <!-- Failed State -->
            <div class="text-center py-12">
                <div class="text-6xl text-red-600 mb-4">❌</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Płatność nieudana</h3>

                @if($errorMessage)
                    <p class="text-red-600 mb-6">{{ $errorMessage }}</p>
                @else
                    <p class="text-gray-600 mb-6">Wystąpił problem podczas przetwarzania płatności</p>
                @endif

                <div class="space-x-4">
                    <button wire:click="retryPayment"
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors">
                        Spróbuj ponownie
                    </button>
                    <a href="{{ route('profile.bookings') }}"
                       class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                        Powrót do rezerwacji
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Help Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-8">
        <div class="flex items-start">
            <div class="text-2xl mr-4">💡</div>
            <div>
                <h3 class="font-semibold text-blue-900 mb-2">Potrzebujesz pomocy?</h3>
                <p class="text-blue-800 text-sm mb-2">
                    Jeśli masz problemy z płatnością lub pytania dotyczące rezerwacji, skontaktuj się z nami.
                </p>
                <div class="text-sm text-blue-700">
                    <div>📧 Email: pomoc@pethelp.pl</div>
                    <div>📞 Telefon: +48 123 456 789</div>
                </div>
            </div>
        </div>
    </div>
</div>
