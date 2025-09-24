<!-- Quick Service Form Widget -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Szybkie dodawanie</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Utwórz nową usługę</p>
                </div>
            </div>
            <button wire:click="toggleForm"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                {{ $showForm ? 'Anuluj' : 'Dodaj usługę' }}
            </button>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6">
        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        @if ($showForm)
            <form wire:submit.prevent="createService" class="space-y-4">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nazwa usługi *
                    </label>
                    <input type="text"
                           id="title"
                           wire:model="title"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="np. Spacery z psem w parku">
                    @error('title')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Kategoria *
                    </label>
                    <select wire:model="category_id"
                            id="category_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Wybierz kategorię</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Opis *
                    </label>
                    <textarea wire:model="description"
                              id="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Opisz swoją usługę..."></textarea>
                    @error('description')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price_per_hour" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Cena za godzinę (zł) *
                    </label>
                    <input type="number"
                           id="price_per_hour"
                           wire:model="price_per_hour"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="50.00">
                    @error('price_per_hour')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Pet Types -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rodzaje zwierząt *
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_types" value="dog" class="mr-2">
                            <span class="text-sm">🐕 Psy</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_types" value="cat" class="mr-2">
                            <span class="text-sm">🐱 Koty</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_types" value="bird" class="mr-2">
                            <span class="text-sm">🐦 Ptaki</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_types" value="rabbit" class="mr-2">
                            <span class="text-sm">🐰 Króliki</span>
                        </label>
                    </div>
                    @error('pet_types')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Pet Sizes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rozmiary zwierząt *
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_sizes" value="small" class="mr-2">
                            <span class="text-sm">Małe</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_sizes" value="medium" class="mr-2">
                            <span class="text-sm">Średnie</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="pet_sizes" value="large" class="mr-2">
                            <span class="text-sm">Duże</span>
                        </label>
                    </div>
                    @error('pet_sizes')
                        <span class="text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Lokalizacja usługi
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="home_service" class="mr-2">
                            <span class="text-sm">🏡 U klienta</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="sitter_home" class="mr-2">
                            <span class="text-sm">🏠 U opiekuna</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                        <span wire:loading.remove>Utwórz usługę</span>
                        <span wire:loading>Tworzenie...</span>
                    </button>
                </div>
            </form>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Szybkie dodawanie</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4 text-sm">
                    Utwórz nową usługę bezpośrednio z dashboardu
                </p>
                <button wire:click="toggleForm"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj nową usługę
                </button>
            </div>
        @endif
    </div>
</div>
