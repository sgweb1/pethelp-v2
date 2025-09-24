<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header with Category Info -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <button wire:click="$redirect(route('sitter-services.create'))"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->category()->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $this->category()->description }}</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if (session('info'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-blue-800">{{ session('info') }}</p>
        </div>
    @endif

    <!-- Fake Data Button for Testing -->
    <div class="mb-6">
        <button
            wire:click="fillWithFakeData"
            type="button"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm"
        >
            🤖 Wypełnij przykładowymi danymi
        </button>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Podstawowe informacje</h3>

            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nazwa usługi *
                    </label>
                    <input
                        wire:model="title"
                        type="text"
                        id="title"
                        placeholder="np. Opieka nad psami w domu właściciela - Warszawa Mokotów"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Opis usługi *
                    </label>
                    <textarea
                        wire:model="description"
                        id="description"
                        rows="4"
                        placeholder="Opisz szczegółowo swoją usługę: doświadczenie, co obejmuje opieka, specjalizacje..."
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    ></textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cennik</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za godzinę (PLN)
                    </label>
                    <input
                        wire:model="price_per_hour"
                        type="number"
                        step="0.01"
                        min="10"
                        max="500"
                        id="price_per_hour"
                        placeholder="25.00"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('price_per_hour') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price_per_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za dzień (PLN)
                    </label>
                    <input
                        wire:model="price_per_day"
                        type="number"
                        step="0.01"
                        min="50"
                        max="2000"
                        id="price_per_day"
                        placeholder="150.00"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('price_per_day') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            @error('price') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
        </div>

        <!-- Service Specifics -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Zakres usług</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Service checkboxes -->
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input wire:model="feeding_included" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Karmienie zgodnie z harmonogramem</span>
                    </label>

                    <label class="flex items-center">
                        <input wire:model="walking_included" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Spacery (krótkie wyprowadzenia)</span>
                    </label>

                    <label class="flex items-center">
                        <input wire:model="play_time" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Czas na zabawę i aktywność</span>
                    </label>

                    <label class="flex items-center">
                        <input wire:model="basic_grooming" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Podstawowa pielęgnacja</span>
                    </label>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center">
                        <input wire:model="overnight_care" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Możliwość opieki nocnej</span>
                    </label>

                    <div>
                        <label for="max_pets" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maksymalna liczba zwierząt
                        </label>
                        <select wire:model="max_pets" id="max_pets" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'zwierzę' : 'zwierząt' }}</option>
                            @endfor
                        </select>
                        @error('max_pets') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Special Notes -->
            <div class="mt-6">
                <label for="special_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Dodatkowe informacje
                </label>
                <textarea
                    wire:model="special_notes"
                    id="special_notes"
                    rows="3"
                    placeholder="Dodatkowe uwagi, ograniczenia, specjalizacje..."
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                ></textarea>
                @error('special_notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Pet Types & Sizes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Typy zwierząt i rozmiary</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Pet Types -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Rodzaje zwierząt *</h4>
                    <div class="space-y-2">
                        @foreach($this->petTypes as $petType)
                        <label class="flex items-center">
                            <input
                                wire:model="pet_types"
                                type="checkbox"
                                value="{{ $petType->slug }}"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $petType->icon }} {{ $petType->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('pet_types') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Pet Sizes -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Rozmiary zwierząt *</h4>
                    <div class="space-y-2">
                        @foreach($this->petSizeOptions as $value => $label)
                        <label class="flex items-center">
                            <input
                                wire:model="pet_sizes"
                                type="checkbox"
                                value="{{ $value }}"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('pet_sizes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lokalizacja</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Adres *
                    </label>
                    <input
                        wire:model="address"
                        type="text"
                        id="address"
                        placeholder="ul. Przykładowa 123"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Miasto *
                    </label>
                    <input
                        wire:model="city"
                        type="text"
                        id="city"
                        placeholder="Warszawa"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="voivodeship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Województwo *
                    </label>
                    <select
                        wire:model="voivodeship"
                        id="voivodeship"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Wybierz województwo</option>
                        @foreach($this->voivodeshipOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('voivodeship') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Szerokość geograficzna *
                        </label>
                        <input
                            wire:model="latitude"
                            type="number"
                            step="any"
                            id="latitude"
                            placeholder="52.229676"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                        @error('latitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Długość geograficzna *
                        </label>
                        <input
                            wire:model="longitude"
                            type="number"
                            step="any"
                            id="longitude"
                            placeholder="21.012229"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                        @error('longitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between">
            <button
                type="button"
                wire:click="$redirect(route('sitter-services.create'))"
                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
                Wróć do wyboru kategorii
            </button>

            <button
                type="submit"
                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Dodaj usługę</span>
                <span wire:loading>Dodawanie...</span>
            </button>
        </div>
    </form>
</div>