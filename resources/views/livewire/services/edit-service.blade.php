@section('title', 'Edycja usługi - PetHelp')

@section('header-title')
    <div class="flex items-center">
        <a href="{{ route('sitter-services.index') }}" class="text-blue-600 hover:text-blue-700 mr-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Edytuj usługę</h1>
        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $service->category->name }}
        </span>
    </div>
@endsection

<div class="space-y-6">
    <!-- Service Category Info -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">{{ $service->category->icon ?? '🐾' }}</span>
            <div>
                <h3 class="font-medium text-blue-900 dark:text-blue-100">{{ $service->category->name }}</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">{{ $service->category->description }}</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Edit Form -->
    <form wire:submit.prevent="validateAndSave" class="space-y-8">

        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Podstawowe informacje</h2>

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tytuł oferty *
                </label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="np. Profesjonalna opieka nad psami w Warszawie"
                    maxlength="100"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ strlen($title ?? '') }}/100 znaków</p>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Opis usługi *
                </label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="5"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Opisz swoją usługę, doświadczenie, co oferujesz..."
                    maxlength="1000"
                ></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ strlen($description ?? '') }}/1000 znaków</p>
            </div>
        </div>

        <!-- Pet Preferences -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Preferencje zwierząt</h2>

            <!-- Pet Types -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Rodzaje zwierząt *
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($this->petTypes as $value => $label)
                        <label class="flex items-center space-x-2 p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="pet_types"
                                value="{{ $value }}"
                                class="text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('pet_types')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pet Sizes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Rozmiary zwierząt *
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($this->petSizes as $value => $label)
                        <label class="flex items-center space-x-2 p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="pet_sizes"
                                value="{{ $value }}"
                                class="text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('pet_sizes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Max Pets -->
            <div class="mb-6">
                <label for="max_pets" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Maksymalna liczba zwierząt jednocześnie *
                </label>
                <select
                    id="max_pets"
                    wire:model="max_pets"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">Wybierz liczbę</option>
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'zwierzę' : ($i <= 4 ? 'zwierzęta' : 'zwierząt') }}</option>
                    @endfor
                </select>
                @error('max_pets')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Service Type -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Typ usługi</h2>

            <div class="space-y-4">
                <label class="flex items-start space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="home_service"
                        class="mt-1 text-blue-600 focus:ring-blue-500"
                    >
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">🏡 Usługa u klienta</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Oferujesz opiekę w domu klienta</p>
                    </div>
                </label>

                <label class="flex items-start space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="sitter_home"
                        class="mt-1 text-blue-600 focus:ring-blue-500"
                    >
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">🏠 Usługa u opiekuna</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Oferujesz opiekę w swoim domu</p>
                    </div>
                </label>
            </div>

            @error('service_type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pricing -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Cennik</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Ustaw przynajmniej jedną cenę. Pozostaw puste pola dla opcji, których nie oferujesz.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Price per hour -->
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za godzinę (zł)
                    </label>
                    <input
                        type="number"
                        id="price_per_hour"
                        wire:model="price_per_hour"
                        step="0.01"
                        min="0"
                        max="1000"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="np. 25.00"
                    >
                    @error('price_per_hour')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price per day -->
                <div>
                    <label for="price_per_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za dzień (zł)
                    </label>
                    <input
                        type="number"
                        id="price_per_day"
                        wire:model="price_per_day"
                        step="0.01"
                        min="0"
                        max="2000"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="np. 150.00"
                    >
                    @error('price_per_day')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price per week -->
                <div>
                    <label for="price_per_week" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cena za tydzień (zł)
                    </label>
                    <input
                        type="number"
                        id="price_per_week"
                        wire:model="price_per_week"
                        step="0.01"
                        min="0"
                        max="10000"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="np. 800.00"
                    >
                    @error('price_per_week')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @error('pricing')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-6">
            <a
                href="{{ route('sitter-services.index') }}"
                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
            >
                Anuluj
            </a>

            <button
                type="submit"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Zaktualizuj usługę</span>
                <span wire:loading>Zapisywanie...</span>
            </button>
        </div>
    </form>
</div>