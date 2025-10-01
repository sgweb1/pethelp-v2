<div>
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Moje Zwierzęta</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Zarządzaj swoimi pupilami</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('profile.pets.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj zwierzę
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    @php $stats = $this->stats; @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-2xl">🐾</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Wszystkie zwierzęta</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-2xl">✅</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktywne</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-2xl">📊</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rodzaje</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($stats['types']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Szukaj</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       id="search"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500"
                       placeholder="Nazwa, rasa...">
            </div>

            <!-- Filter by Type -->
            <div>
                <label for="filterType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Typ zwierzęcia</label>
                <select wire:model.live="filterType"
                        id="filterType"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Wszystkie</option>
                    @foreach($this->petTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label for="sortBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sortuj</label>
                <select wire:model.live="sortBy"
                        id="sortBy"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    <option value="name">Nazwa</option>
                    <option value="created_at">Data dodania</option>
                    <option value="birth_date">Wiek</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Pets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @forelse($this->pets as $pet)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                <!-- Pet Image -->
                <div class="relative">
                    <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-400 rounded-t-xl flex items-center justify-center">
                        @if($pet->photo_url && $pet->photo_url !== asset('images/pet-placeholder.png'))
                            <img src="{{ $pet->photo_url }}"
                                 alt="{{ $pet->name }}"
                                 class="w-full h-full object-cover rounded-t-xl">
                        @else
                            <span class="text-white text-4xl font-bold">
                                {{ substr($pet->name, 0, 1) }}
                            </span>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pet->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' }}">
                            {{ $pet->is_active ? 'Aktywne' : 'Nieaktywne' }}
                        </span>
                    </div>
                </div>

                <!-- Pet Info -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pet->name }}</h3>
                        <div class="flex items-center space-x-2">
                            <button wire:click="togglePetStatus({{ $pet->id }})"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    title="{{ $pet->is_active ? 'Dezaktywuj' : 'Aktywuj' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Typ:</span> {{ $pet->petType?->name ?? 'Nieznany' }}
                        </p>
                        @if($pet->breed)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Rasa:</span> {{ $pet->breed }}
                            </p>
                        @endif
                        @if($pet->birth_date)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Wiek:</span> {{ $pet->birth_date->age }} lat
                            </p>
                        @endif
                        @if($pet->gender)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Płeć:</span> {{ $pet->gender_label }}
                            </p>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('profile.pets.edit', $pet) }}"
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Edytuj
                        </a>
                        <button wire:click="confirmDelete({{ $pet->id }})"
                                class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Usuń
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🐕</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Brak zwierząt</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Dodaj swojego pierwszego pupila do platformy</p>
                    <a href="{{ route('profile.pets.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj zwierzę
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->pets->hasPages())
        <div class="mt-8">
            {{ $this->pets->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ open: @entangle('showDeleteModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Usuń zwierzę
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Czy na pewno chcesz usunąć zwierzę <strong>{{ $petToDelete?->name }}</strong>?
                                Ta akcja nie może być cofnięta.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            wire:click="deletePet"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Usuń
                    </button>
                    <button type="button"
                            wire:click="cancelDelete"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white">
                        Anuluj
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
