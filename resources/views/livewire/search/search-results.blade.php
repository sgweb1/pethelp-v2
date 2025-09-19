<div class="space-y-6">
    {{-- Results Header --}}
    <div class="flex items-center justify-between bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="text-sm text-gray-600">
                Znaleziono <span class="font-semibold text-gray-900">{{ $this->resultsCount }}</span>
                @if($this->resultsCount === 1) usługę @elseif($this->resultsCount < 5) usługi @else usług @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Loading indicator --}}
            <div wire:loading class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Ładowanie...</span>
            </div>
        </div>
    </div>

    {{-- Services Grid --}}
    @if($this->services->count() > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($this->services as $service)
                <div wire:key="service-{{ $service->id }}"
                     class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        {{-- Service Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900 line-clamp-2">
                                    {{ $service->title }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $service->category->name }}
                                </p>
                            </div>
                            @if($service->category->icon)
                                <div class="text-2xl ml-3">
                                    {{ $service->category->icon }}
                                </div>
                            @endif
                        </div>

                        {{-- Sitter Info --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ substr($service->sitter->profile->first_name ?? $service->sitter->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $service->sitter->profile->first_name ?? '' }}
                                    {{ $service->sitter->profile->last_name ?? $service->sitter->name }}
                                </p>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    @if($service->sitter->location)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $service->sitter->location->city }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Service Description --}}
                        <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                            {{ $service->description }}
                        </p>

                        {{-- Service Details --}}
                        <div class="space-y-2 mb-4">
                            @if($service->price_per_hour)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Za godzinę:</span>
                                    <span class="font-semibold text-gray-900">{{ $service->price_per_hour }} zł</span>
                                </div>
                            @endif
                            @if($service->price_per_day)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Za dzień:</span>
                                    <span class="font-semibold text-gray-900">{{ $service->price_per_day }} zł</span>
                                </div>
                            @endif
                        </div>

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($service->sitter->profile && $service->sitter->profile->is_verified)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Zweryfikowany
                                </span>
                            @endif
                            @if($service->sitter->profile && $service->sitter->profile->has_insurance)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Ubezpieczony
                                </span>
                            @endif
                            @if($service->sitter->profile && $service->sitter->profile->experience_years > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $service->sitter->profile->experience_years }} lat doświadczenia
                                </span>
                            @endif
                        </div>

                        {{-- Action Button --}}
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                Zobacz szczegóły
                            </button>
                            <button class="px-3 py-2 text-gray-400 hover:text-red-500 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($this->services->hasPages())
            <div class="mt-8">
                {{ $this->services->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Brak wyników</h3>
            <p class="mt-1 text-sm text-gray-500">
                Nie znaleziono usług odpowiadających Twoim kryteriom wyszukiwania.
            </p>
            <div class="mt-6">
                <button wire:click="$dispatch('reset-filters')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Wyczyść filtry
                </button>
            </div>
        </div>
    @endif

    {{-- Loading overlay --}}
    <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Wyszukiwanie...</span>
        </div>
    </div>
</div>
