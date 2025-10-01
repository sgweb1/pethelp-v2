{{--
    Uniwersalna karta statystyki dla dashboard PetHelp

    Wyświetla statystyki w postaci atrakcyjnej karty z ikoną, wartością
    i opcjonalnymi wskaźnikami trendu. Obsługuje różne warianty kolorystyczne
    i efekty hover.

    @param string $title - Tytuł statystyki
    @param string|int $value - Wartość do wyświetlenia
    @param string $icon - Emoji lub SVG ikony
    @param string $color - Wariant kolorystyczny (blue|green|purple|yellow|red)
    @param string|null $description - Opcjonalny opis pod wartością
    @param array|null $trend - Opcjonalne dane trendu ['direction' => 'up'|'down', 'value' => '5%']
    @param string|null $route - Opcjonalna trasa do przekierowania po kliknięciu
    @param bool $loading - Czy wyświetlić stan ładowania
--}}
@props([
    'title' => '',
    'value' => '',
    'icon' => '',
    'color' => 'blue',
    'description' => null,
    'trend' => null,
    'route' => null,
    'loading' => false
])

@php
/**
 * Definiowanie wariantów kolorystycznych dla różnych stanów karty.
 * Każdy wariant zawiera klasy dla tła, tekstu i ikon.
 */
$colorVariants = [
    'blue' => [
        'bg' => 'bg-blue-50/80 dark:bg-blue-900/20',
        'text' => 'text-blue-600 dark:text-blue-400',
        'icon' => 'bg-blue-100 dark:bg-blue-800/50',
        'border' => 'border-blue-200 dark:border-blue-700',
        'hover' => 'hover:bg-blue-100/50 dark:hover:bg-blue-900/30'
    ],
    'green' => [
        'bg' => 'bg-green-50/80 dark:bg-green-900/20',
        'text' => 'text-green-600 dark:text-green-400',
        'icon' => 'bg-green-100 dark:bg-green-800/50',
        'border' => 'border-green-200 dark:border-green-700',
        'hover' => 'hover:bg-green-100/50 dark:hover:bg-green-900/30'
    ],
    'purple' => [
        'bg' => 'bg-purple-50/80 dark:bg-purple-900/20',
        'text' => 'text-purple-600 dark:text-purple-400',
        'icon' => 'bg-purple-100 dark:bg-purple-800/50',
        'border' => 'border-purple-200 dark:border-purple-700',
        'hover' => 'hover:bg-purple-100/50 dark:hover:bg-purple-900/30'
    ],
    'yellow' => [
        'bg' => 'bg-yellow-50/80 dark:bg-yellow-900/20',
        'text' => 'text-yellow-600 dark:text-yellow-400',
        'icon' => 'bg-yellow-100 dark:bg-yellow-800/50',
        'border' => 'border-yellow-200 dark:border-yellow-700',
        'hover' => 'hover:bg-yellow-100/50 dark:hover:bg-yellow-900/30'
    ],
    'red' => [
        'bg' => 'bg-red-50/80 dark:bg-red-900/20',
        'text' => 'text-red-600 dark:text-red-400',
        'icon' => 'bg-red-100 dark:bg-red-800/50',
        'border' => 'border-red-200 dark:border-red-700',
        'hover' => 'hover:bg-red-100/50 dark:hover:bg-red-900/30'
    ]
];

$colors = $colorVariants[$color] ?? $colorVariants['blue'];
$isClickable = !empty($route);
$containerTag = $isClickable ? 'a' : 'div';
$containerAttrs = $isClickable ? "href='" . route($route) . "'" : '';
@endphp

<{{ $containerTag }} {{ $containerAttrs }}
    class="group block relative bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border {{ $colors['border'] }} rounded-2xl p-6 shadow-soft hover:shadow-medium transition-all duration-300 {{ $isClickable ? 'cursor-pointer transform hover:scale-[1.02] hover:-translate-y-1' : '' }} {{ $colors['hover'] }}"
    {{ $attributes }}
>
    {{-- Loading State Overlay --}}
    @if($loading)
        <div class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl flex items-center justify-center z-10">
            <div class="flex flex-col items-center space-y-2">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Ładowanie...</span>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            {{-- Tytuł karty --}}
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2 truncate">
                {{ $title }}
            </p>

            {{-- Główna wartość z trendem --}}
            <div class="flex items-baseline space-x-3 mb-1">
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white truncate">
                    {{ $value }}
                </h3>

                {{-- Wskaźnik trendu --}}
                @if($trend && isset($trend['direction']) && isset($trend['value']))
                    <span class="flex items-center text-sm font-medium {{ $trend['direction'] === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        @if($trend['direction'] === 'up')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7M17 17H7"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10M7 7h10"></path>
                            </svg>
                        @endif
                        {{ $trend['value'] }}
                    </span>
                @endif
            </div>

            {{-- Opcjonalny opis --}}
            @if($description)
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ $description }}
                </p>
            @endif
        </div>

        {{-- Ikona po prawej stronie --}}
        @if($icon)
            <div class="flex-shrink-0 ml-4">
                <div class="w-16 h-16 {{ $colors['icon'] }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    @if(str_starts_with($icon, '<svg'))
                        <div class="w-8 h-8 {{ $colors['text'] }}">
                            {!! $icon !!}
                        </div>
                    @else
                        <span class="text-3xl">{{ $icon }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Wskaźnik klikalności --}}
    @if($isClickable)
        <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <svg class="w-4 h-4 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    @endif

    {{-- Efekt świetlny na hover --}}
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
</{{ $containerTag }}>