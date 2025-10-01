{{--
    Widget statystyk dashboard PetHelp

    Wyświetla kluczowe metryki w postaci responsywnych kart z animacjami
    i wskaźnikami trendu. Dostosowuje się automatycznie do roli użytkownika.
--}}
<div class="space-y-6"
     x-data="{
         loading: @entangle('loading'),
         autoRefresh: @entangle('autoRefresh'),
         refreshInterval: @entangle('refreshInterval'),
         lastUpdated: @entangle('lastUpdated'),
         refreshTimer: null
     }"
     x-init="
         if (autoRefresh) {
             refreshTimer = setInterval(() => {
                 $wire.refreshData();
             }, refreshInterval * 1000);
         }
     "
     x-on:data-refreshed="lastUpdated = new Date().toLocaleString('pl-PL')"
     wire:loading.class="opacity-50 pointer-events-none">

    {{-- Header sekcji --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Przegląd statystyk
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="'Ostatnia aktualizacja: ' + lastUpdated"></p>
        </div>

        {{-- Kontrolki odświeżania --}}
        <div class="flex items-center space-x-3">
            {{-- Toggle auto-refresh --}}
            <button @click="autoRefresh = !autoRefresh;
                            if (autoRefresh) {
                                refreshTimer = setInterval(() => $wire.refreshData(), refreshInterval * 1000);
                            } else {
                                clearInterval(refreshTimer);
                            }"
                    class="flex items-center space-x-2 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                    :class="autoRefresh
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                        : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span x-text="autoRefresh ? 'Auto' : 'Manual'"></span>
            </button>

            {{-- Manual refresh --}}
            <button wire:click="refreshData"
                    class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    :disabled="loading">
                <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Grid statystyk --}}
    @php
        $statsCount = $this->stats->count();
        $gridClass = match($layout) {
            'grid-2' => 'grid-cols-1 sm:grid-cols-2',
            'grid-3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
            'grid-4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
            'auto' => $statsCount <= 2 ? 'grid-cols-1 sm:grid-cols-2'
                    : ($statsCount <= 3 ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
                    : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'),
            default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
        };
    @endphp

    <div class="grid {{ $gridClass }} gap-6">
        @forelse($this->stats as $index => $stat)
            <div class="animate-fadeInUp"
                 style="animation-delay: {{ $index * 100 }}ms;">
                <x-dashboard.molecules.stat-card
                    :title="$stat['title']"
                    :value="$stat['value']"
                    :icon="$stat['icon']"
                    :color="$stat['color']"
                    :description="$stat['description'] ?? null"
                    :trend="$stat['trend'] ?? null"
                    :route="$stat['route'] ?? null"
                    :loading="$loading" />
            </div>
        @empty
            {{-- Pusty stan --}}
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Brak danych do wyświetlenia
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        Uzupełnij swój profil, aby zobaczyć statystyki aktywności.
                    </p>
                    <x-ui.button variant="primary" size="sm">
                        <a href="{{ route('profile.edit') }}" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Uzupełnij profil
                        </a>
                    </x-ui.button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Podsumowanie i dodatkowe informacje --}}
    @if($this->stats->isNotEmpty())
        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-soft border border-white/20 dark:border-gray-700/50 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    {{-- Wskaźnik typu użytkownika --}}
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full {{ $type === 'owner' ? 'bg-blue-500' : ($type === 'sitter' ? 'bg-purple-500' : 'bg-green-500') }}"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @switch($type)
                                @case('owner')
                                    Właściciel zwierząt
                                    @break
                                @case('sitter')
                                    Opiekun zwierząt
                                    @break
                                @case('combined')
                                    Właściciel i Opiekun
                                    @break
                                @default
                                    Nowy użytkownik
                            @endswitch
                        </span>
                    </div>

                    {{-- Okres trendów --}}
                    @if($showTrends)
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            <span>Trendy {{ $trendPeriod }} dni</span>
                        </div>
                    @endif
                </div>

                {{-- Linki do szczegółów --}}
                <div class="flex items-center space-x-3">
                    @if($type === 'owner' || $type === 'combined')
                        <a href="{{ route('profile.pets.index') }}"
                           class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            Moje zwierzęta →
                        </a>
                    @endif

                    @if($type === 'sitter' || $type === 'combined')
                        <a href="{{ route('profile.services.index') }}"
                           class="text-sm text-purple-600 hover:text-purple-500 dark:text-purple-400 dark:hover:text-purple-300">
                            Moje usługi →
                        </a>
                    @endif

                    <a href="{{ route('profile.bookings') }}"
                       class="text-sm text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                        Wszystkie zlecenia →
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Styles dla animacji --}}
@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }

    /* Responsive grid improvements */
    @media (max-width: 640px) {
        .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-4 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    /* Dark mode enhancements */
    @media (prefers-color-scheme: dark) {
        .backdrop-blur-md {
            backdrop-filter: blur(16px) brightness(1.1);
        }
    }

    /* Loading shimmer effect */
    .loading-shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    /* Hover enhancements for accessibility */
    @media (hover: hover) {
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .animate-fadeInUp,
        .transition-all,
        .transition-colors,
        .transition-transform {
            animation: none !important;
            transition: none !important;
        }
    }
</style>
@endpush