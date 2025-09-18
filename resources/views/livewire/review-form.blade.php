<div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg p-6">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-2">
            {{ $existingReview ? 'Edytuj recenzję' : 'Napisz recenzję' }}
        </h3>
        <p class="text-gray-600">
            {{ $existingReview ? 'Możesz edytować swoją recenzję (w ciągu 24h od utworzenia)' : 'Oceń współpracę z ' . $this->reviewee->name }}
        </p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="submit">
        <!-- Booking Info -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600">Usługa:</span>
                    <div>{{ $booking->service->title }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-600">Data:</span>
                    <div>{{ $booking->start_date->format('d.m.Y H:i') }}</div>
                </div>
                <div>
                    <span class="font-medium text-gray-600">{{ $booking->owner_id === auth()->id() ? 'Opiekun:' : 'Właściciel:' }}</span>
                    <div>{{ $this->reviewee->name }}</div>
                </div>
            </div>
        </div>

        <!-- Rating -->
        <div class="mb-6">
            <label for="rating" class="block text-sm font-medium text-gray-700 mb-3">
                Ocena <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center space-x-2">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            wire:click="$set('rating', {{ $i }})"
                            class="text-3xl transition-colors {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400">
                        ⭐
                    </button>
                @endfor
                @if($rating)
                    <span class="ml-3 text-sm text-gray-600">
                        {{ $rating }}/5 -
                        @switch($rating)
                            @case(1) Bardzo słaba @break
                            @case(2) Słaba @break
                            @case(3) Średnia @break
                            @case(4) Dobra @break
                            @case(5) Bardzo dobra @break
                        @endswitch
                    </span>
                @endif
            </div>
            @error('rating')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Comment -->
        <div class="mb-6">
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                Komentarz (opcjonalny)
            </label>
            <textarea
                wire:model="comment"
                id="comment"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Opisz swoją opinię o współpracy..."></textarea>
            @error('comment')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Maksymalnie 1000 znaków</p>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-3">
            @if($existingReview && !$this->canEdit)
                <p class="text-sm text-amber-600 mr-auto">
                    ⚠️ Recenzję można edytować tylko w ciągu 24h od utworzenia
                </p>
            @endif

            <button type="button"
                    onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Anuluj
            </button>

            <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                <span wire:loading.remove>
                    {{ $existingReview ? 'Zaktualizuj recenzję' : 'Dodaj recenzję' }}
                </span>
                <span wire:loading>
                    {{ $existingReview ? 'Aktualizuję...' : 'Dodaję...' }}
                </span>
            </button>
        </div>
    </form>
</div>
