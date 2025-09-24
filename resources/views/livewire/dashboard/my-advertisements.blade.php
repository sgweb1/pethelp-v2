<!-- My Advertisements Dashboard Component -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Moje ogłoszenia</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @php
                            try {
                                $adsCount = $this->advertisements->count();
                                echo $adsCount . ' ' . ($adsCount === 1 ? 'ogłoszenie' : 'ogłoszeń');
                            } catch (Exception $e) {
                                echo '0 ogłoszeń';
                            }
                        @endphp
                    </p>
                </div>
            </div>
            <a href="{{ route('advertisements.index') }}"
               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                Zobacz wszystkie
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @php
            try {
                $advertisements = $this->advertisements;
                $adsCount = $advertisements->count();
            } catch (Exception $e) {
                $advertisements = collect();
                $adsCount = 0;
            }
        @endphp

        @if($adsCount > 0)
                <div class="space-y-4">
                    @foreach($this->advertisements as $advertisement)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mr-3">
                                    @if($advertisement->primaryImage?->path)
                                        <img src="{{ asset('storage/' . $advertisement->primaryImage->path) }}"
                                             alt="{{ $advertisement->title }}"
                                             class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <span class="text-lg">{{ $advertisement->advertisementCategory->icon ?? '🏷️' }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $advertisement->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $advertisement->advertisementCategory->name }}
                                    </p>
                                    @if($advertisement->price)
                                        <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                            {{ number_format($advertisement->price, 0) }}{{ $advertisement->currency ?? 'zł' }}
                                            @if($advertisement->price_negotiable)
                                                <span class="text-gray-500">(do negocjacji)</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $advertisement->status === 'published'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                        : ($advertisement->status === 'draft'
                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400') }}">
                                    @switch($advertisement->status)
                                        @case('published')
                                            Opublikowane
                                            @break
                                        @case('draft')
                                            Szkic
                                            @break
                                        @case('pending')
                                            Oczekuje
                                            @break
                                        @case('rejected')
                                            Odrzucone
                                            @break
                                        @default
                                            {{ ucfirst($advertisement->status) }}
                                    @endswitch
                                </span>

                                <!-- Featured Badge -->
                                @if($advertisement->is_featured)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                        Promowane
                                    </span>
                                @endif

                                <!-- Urgent Badge -->
                                @if($advertisement->is_urgent)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        Pilne
                                    </span>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center space-x-1">
                                    <!-- Toggle Status -->
                                    <button wire:click="toggleAdvertisementStatus({{ $advertisement->id }})"
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            title="{{ $advertisement->status === 'published' ? 'Ukryj' : 'Opublikuj' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($advertisement->status === 'published')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            @endif
                                        </svg>
                                    </button>

                                    <!-- Edit -->
                                    <a href="{{ route('advertisements.edit', $advertisement) }}"
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

                <!-- Add New Advertisement Button -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <a href="{{ route('advertisements.create') }}"
                       class="flex items-center justify-center w-full p-3 text-sm font-medium text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj nowe ogłoszenie
                    </a>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Brak ogłoszeń</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Nie masz jeszcze żadnych ogłoszeń. Dodaj swoje pierwsze ogłoszenie.
                    </p>
                    <a href="{{ route('advertisements.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Dodaj pierwsze ogłoszenie
                    </a>
                </div>
            @endif
    </div>
</div>
