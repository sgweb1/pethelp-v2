@section('title', 'Moje usługi - PetHelp')

@section('header-title')
    <div class="flex items-center">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Moje usługi</h1>
        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $this->services->count() }} {{ $this->services->count() === 1 ? 'usługa' : 'usług' }}
        </span>
    </div>
@endsection

<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($this->services->isEmpty())
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brak usług</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Nie masz jeszcze żadnych dodanych usług pet sittingu.</p>
            <a href="{{ route('sitter-services.create') }}"
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Dodaj pierwszą usługę
            </a>
        </div>
    @else
        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($this->services as $service)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <!-- Service Header -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                    {{ $service->title }}
                                </h3>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="mr-2">{{ $service->category->icon ?? '🐾' }}</span>
                                    {{ $service->category->name ?? 'Pet Sitting' }}
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $service->is_active ? 'Aktywna' : 'Nieaktywna' }}
                            </span>
                        </div>
                        <!-- Description -->
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                            {{ Str::limit($service->description, 80) }}
                        </p>

                        <!-- Tags -->
                        <div class="mb-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($service->pet_types ?? [] as $petType)
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs rounded">
                                        {{ ucfirst($petType) }}
                                    </span>
                                @endforeach
                                @foreach($service->pet_sizes ?? [] as $size)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs rounded">
                                        @switch($size)
                                            @case('small') Małe @break
                                            @case('medium') Średnie @break
                                            @case('large') Duże @break
                                            @default {{ $size }} @break
                                        @endswitch
                                    </span>
                                @endforeach
                                @if($service->home_service)
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-xs rounded">
                                        🏡 U klienta
                                    </span>
                                @endif
                                @if($service->sitter_home)
                                    <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300 text-xs rounded">
                                        🏠 U opiekuna
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Price and Actions -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $service->display_price }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Max {{ $service->max_pets }} {{ $service->max_pets === 1 ? 'zwierzę' : 'zwierząt' }}
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('sitter-services.edit', $service) }}"
                                   class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Edytuj
                                </a>
                                <button wire:click="confirmServiceDeletion({{ $service->id }})"
                                        class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    Usuń
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Confirmation Modal -->
    @if($confirmingServiceDeletion && $serviceToDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cancelDeletion"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Usuń usługę
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Czy na pewno chcesz usunąć usługę "<strong>{{ $serviceToDelete->title }}</strong>"?
                                    </p>
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                        Tej operacji nie można cofnąć. Usługa zostanie również usunięta z mapy.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="deleteService"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Usuń usługę</span>
                            <span wire:loading>Usuwanie...</span>
                        </button>
                        <button
                            wire:click="cancelDeletion"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Anuluj
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    </div>
</div>