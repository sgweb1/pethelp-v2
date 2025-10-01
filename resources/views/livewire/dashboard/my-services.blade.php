<!-- My Services Dashboard Component -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Moje usługi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->services->count() }} {{ $this->services->count() === 1 ? 'usługa' : 'usług' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('services.index') }}"
               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                Zobacz wszystkie
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @if($this->services->count() > 0)
            <div class="space-y-4">
                @foreach($this->services as $service)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-lg">{{ $service->category->icon ?? '🛠️' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $service->title }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $service->category->name }}
                                </p>
                                @if($service->price_per_hour)
                                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">
                                        {{ number_format($service->price_per_hour, 0) }}zł/h
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $service->is_active
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                    : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                {{ $service->is_active ? 'Aktywna' : 'Nieaktywna' }}
                            </span>

                            <!-- Actions -->
                            <div class="flex items-center space-x-1">
                                <!-- Toggle Status -->
                                <button wire:click="toggleServiceStatus({{ $service->id }})"
                                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                        title="{{ $service->is_active ? 'Dezaktywuj' : 'Aktywuj' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($service->is_active)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                </button>

                                <!-- Edit -->
                                <a href="{{ route('profile.services.edit', $service) }}"
                                   class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                                   title="Edytuj">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add New Service Button -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <a href="{{ route('profile.services.create') }}"
                   class="flex items-center justify-center w-full p-3 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj nową usługę
                </a>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Brak usług</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Nie masz jeszcze żadnych aktywnych usług. Dodaj swoją pierwszą usługę.
                </p>
                <a href="{{ route('profile.services.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Dodaj pierwszą usługę
                </a>
            </div>
        @endif
    </div>
</div>
