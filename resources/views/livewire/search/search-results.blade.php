<div class="space-y-6">

    {{-- Results Grid/List --}}
    @if($this->items->count() > 0)
        <div class="
            @if($viewMode === 'grid')
                grid gap-6 md:grid-cols-2 lg:grid-cols-3
            @else
                space-y-4
            @endif
        " style="@if($viewMode === 'grid') grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); @endif">
            @foreach($this->items as $item)
                <div wire:key="item-{{ $item->id }}"
                     class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200
                            @if($viewMode === 'list') flex @endif"
                     x-data
                     @mouseenter="$dispatch('highlight-map-marker', { id: {{ $item->id }} })"
                     @mouseleave="$dispatch('highlight-map-marker', { id: null })"
                     @click="$dispatch('focus-map-marker', { id: {{ $item->id }} })"
                     style="cursor: pointer;"
                >
                    <div class="p-6 @if($viewMode === 'list') flex-1 @endif">
                        {{-- Item Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900 line-clamp-2">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $item->category_name }}
                                </p>
                            </div>
                            @if($item->category_icon)
                                <div class="text-2xl ml-3">
                                    {{ $item->category_icon }}
                                </div>
                            @endif
                        </div>

                        {{-- Owner Info --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ substr($item->user->name ?? 'U', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $item->user->name ?? 'Użytkownik' }}
                                </p>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    @if($item->city)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $item->city }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if($item->description_short)
                            <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                                {{ $item->description_short }}
                            </p>
                        @endif

                        {{-- Price Details --}}
                        <div class="space-y-2 mb-4">
                            @if($item->price_from)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Cena od:</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ number_format($item->price_from, 2) }} {{ $item->currency ?? 'zł' }}
                                        @if($item->price_to && $item->price_to != $item->price_from)
                                            - {{ number_format($item->price_to, 2) }} {{ $item->currency ?? 'zł' }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Rating and Stats --}}
                        <div class="flex items-center gap-4 mb-4">
                            @if($item->rating_avg > 0)
                                <div class="flex items-center gap-1">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= floor($item->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600">{{ number_format($item->rating_avg, 1) }}</span>
                                    @if($item->rating_count > 0)
                                        <span class="text-xs text-gray-500">({{ $item->rating_count }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($item->is_featured)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Polecane
                                </span>
                            @endif
                            @if($item->is_urgent)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Pilne
                                </span>
                            @endif
                            @if($item->content_type === 'pet_sitter')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Pet Sitter
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

        {{-- Load More Button --}}
        @if($hasMore)
            <div class="mt-8 text-center">
                <button
                    wire:click="loadMore"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors duration-200"
                >
                    <span wire:loading.remove wire:target="loadMore">
                        Pokaż więcej wyników
                    </span>
                    <span wire:loading wire:target="loadMore" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Ładowanie...
                    </span>
                </button>
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

    {{-- Loading overlay - Airbnb style --}}
    <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3 shadow-xl">
            <svg class="animate-spin h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">Wyszukiwanie najlepszych wyników...</span>
        </div>
    </div>

</div>
