<div class="max-w-4xl mx-auto p-6">
    <!-- Service Header -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <div class="text-3xl">{{ $service->category->icon ?? '🐾' }}</div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $service->title }}</h1>
                <p class="text-gray-600">{{ $service->sitter->name }}</p>
                <p class="text-lg font-semibold text-indigo-600">{{ $service->display_price }}</p>
            </div>
        </div>
        @if($service->description)
            <p class="text-gray-700">{{ $service->description }}</p>
        @endif
    </div>

    <!-- Booking Form -->
    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Zarezerwuj tę usługę</h2>

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @guest
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-blue-600">ℹ️</span>
                    <h3 class="font-semibold text-blue-900">Wymagane logowanie</h3>
                </div>
                <p class="text-blue-800 mb-4">Aby dokonać rezerwacji, musisz się zalogować lub założyć konto.</p>
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Zaloguj się
                    </a>
                    <a href="{{ route('register') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Zarejestruj się
                    </a>
                </div>
            </div>
        @endguest

        @auth
            <form wire:submit="submit" class="space-y-6">
                <!-- Pet Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wybierz zwierzę</label>
                    @if($this->user_pets->count() > 0)
                        <select wire:model="pet_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Wybierz zwierzę...</option>
                            @foreach($this->user_pets as $pet)
                                <option value="{{ $pet->id }}">
                                    {{ $pet->name }} ({{ $pet->type_label }}, {{ $pet->size_label }})
                                </option>
                            @endforeach
                        </select>
                        @error('pet_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800">Nie masz jeszcze dodanych zwierząt. <a href="{{ route('dashboard') }}" class="underline">Dodaj zwierzę</a> aby móc dokonać rezerwacji.</p>
                        </div>
                    @endif
                </div>

                @if($this->user_pets->count() > 0)
                    <!-- Date and Time Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Date & Time -->
                        <div class="space-y-4">
                            <h3 class="font-semibold text-gray-900">Data i godzina rozpoczęcia</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data rozpoczęcia</label>
                                <input type="date" wire:model.live="start_date"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       min="{{ now()->addDay()->format('Y-m-d') }}">
                                @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Godzina rozpoczęcia</label>
                                <input type="time" wire:model.live="start_time"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- End Date & Time -->
                        <div class="space-y-4">
                            <h3 class="font-semibold text-gray-900">Data i godzina zakończenia</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data zakończenia</label>
                                <input type="date" wire:model.live="end_date"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       min="{{ $start_date }}">
                                @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Godzina zakończenia</label>
                                <input type="time" wire:model.live="end_time"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Type Selection (if service has both hourly and daily rates) -->
                    @if($service->price_per_hour && $service->price_per_day)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sposób rozliczenia</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="pricing_type" value="hour" class="mr-2">
                                    <span>Godzinowo ({{ $service->price_per_hour }}zł/h)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="pricing_type" value="day" class="mr-2">
                                    <span>Dziennie ({{ $service->price_per_day }}zł/dzień)</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- Special Instructions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dodatkowe uwagi (opcjonalnie)</label>
                        <textarea wire:model="special_instructions"
                                  rows="4"
                                  placeholder="Opisz specjalne potrzeby swojego zwierzęcia, preferencje dotyczące opieki, itp."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Podsumowanie</h3>
                        <div class="space-y-2">
                            @if($start_date && $end_date && $start_time && $end_time)
                                @php
                                    $startDateTime = \Carbon\Carbon::parse($start_date . ' ' . $start_time);
                                    $endDateTime = \Carbon\Carbon::parse($end_date . ' ' . $end_time);
                                @endphp
                                <div class="flex justify-between">
                                    <span>Okres:</span>
                                    <span>{{ $startDateTime->format('d.m.Y H:i') }} - {{ $endDateTime->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($pricing_type === 'hour')
                                    <div class="flex justify-between">
                                        <span>Czas trwania:</span>
                                        <span>{{ $startDateTime->diffInHours($endDateTime) }} godzin</span>
                                    </div>
                                @else
                                    <div class="flex justify-between">
                                        <span>Liczba dni:</span>
                                        <span>{{ $startDateTime->diffInDays($endDateTime) ?: 1 }} dni</span>
                                    </div>
                                @endif
                            @endif
                            <div class="flex justify-between text-lg font-semibold border-t pt-2">
                                <span>Szacowana cena:</span>
                                <span class="text-indigo-600">{{ number_format($estimated_price, 2) }} zł</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4">
                        <button type="submit"
                                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-semibold"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Zarezerwuj bez płatności</span>
                            <span wire:loading>Przetwarzanie...</span>
                        </button>
                        <button type="button"
                                wire:click="createAndPay"
                                class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>💳 Zarezerwuj i zapłać</span>
                            <span wire:loading>Przetwarzanie...</span>
                        </button>
                    </div>
                @endif
            </form>
        @endauth
    </div>

    <!-- Back to Service -->
    <div class="mt-8 text-center">
        <a href="{{ route('sitter.show', $service->sitter) }}" class="inline-flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors">
            <span>←</span>
            <span>Powrót do profilu opiekuna</span>
        </a>
    </div>
</div>
