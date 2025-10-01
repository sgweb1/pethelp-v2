{{--
    Reusable Tooltip Component dla Pet Sitter Wizard

    Komponent tooltip'a dopasowany do kolorystyki wizard'a z gradientami
    purple/pink i emerald/teal używanymi w formularzu.

    @param string $position - Pozycja tooltip'a: 'top', 'bottom', 'left', 'right' (default: 'top')
    @param string $trigger - Trigger dla tooltip'a: 'hover', 'focus', 'click' (default: 'hover')
    @param string $theme - Motyw kolorystyczny: 'primary', 'secondary', 'success' (default: 'primary')
    @param string $size - Rozmiar: 'sm', 'md', 'lg' (default: 'md')
    @param bool $arrow - Czy pokazać strzałkę (default: true)
    @param string $class - Dodatkowe klasy CSS
--}}

@props([
    'position' => 'top',
    'trigger' => 'hover',
    'theme' => 'primary',
    'size' => 'md',
    'arrow' => true,
    'class' => ''
])

@php
    // Mapowanie pozycji na klasy CSS
    $positionClasses = [
        'top' => 'bottom-full left-1/2 transform -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 transform -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 transform -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 transform -translate-y-1/2 ml-2'
    ];

    // Mapowanie trigger'ów na eventy Alpine
    $triggerEvents = [
        'hover' => 'group-hover:opacity-100 group-hover:visible',
        'focus' => 'group-focus-within:opacity-100 group-focus-within:visible',
        'click' => 'group-click:opacity-100 group-click:visible'
    ];

    // Mapowanie motywów na kolory (zgodne z wizard'em)
    $themeClasses = [
        'primary' => 'bg-gradient-to-r from-purple-600 to-pink-600 text-white',
        'secondary' => 'bg-gradient-to-r from-gray-700 to-gray-800 text-gray-100',
        'success' => 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white'
    ];

    // Mapowanie rozmiarów
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs max-w-xs',
        'md' => 'px-3 py-2 text-xs max-w-sm',
        'lg' => 'px-4 py-3 text-sm max-w-md'
    ];

    // Klasy strzałki dla różnych pozycji
    $arrowClasses = [
        'top' => 'top-full left-1/2 transform -translate-x-1/2 border-l-transparent border-r-transparent border-b-transparent',
        'bottom' => 'bottom-full left-1/2 transform -translate-x-1/2 border-l-transparent border-r-transparent border-t-transparent',
        'left' => 'left-full top-1/2 transform -translate-y-1/2 border-t-transparent border-b-transparent border-r-transparent',
        'right' => 'right-full top-1/2 transform -translate-y-1/2 border-t-transparent border-b-transparent border-l-transparent'
    ];

    $positionClass = $positionClasses[$position] ?? $positionClasses['top'];
    $triggerClass = $triggerEvents[$trigger] ?? $triggerEvents['hover'];
    $themeClass = $themeClasses[$theme] ?? $themeClasses['primary'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="relative group {{ $class }}">
    {{-- Trigger Element --}}
    <div class="cursor-help w-full">
        {{ $slot }}
    </div>

    {{-- Tooltip Content --}}
    <div class="absolute {{ $positionClass }} {{ $themeClass }} {{ $sizeClass }} rounded-lg shadow-lg opacity-0 invisible {{ $triggerClass }} transition-all duration-200 z-50 pointer-events-none"
         role="tooltip">

        {{-- Tooltip Text --}}
        <div class="font-medium">
            {{ $tooltip }}
        </div>

        {{-- Optional Subtitle --}}
        @isset($subtitle)
            <div class="text-xs opacity-90 mt-1">
                {{ $subtitle }}
            </div>
        @endisset

        {{-- Arrow (jeśli włączona) --}}
        @if($arrow)
            <div class="absolute w-0 h-0 {{ $arrowClasses[$position] ?? $arrowClasses['top'] }}"
                 style="
                    @if($position === 'top')
                        border-left: 6px solid transparent;
                        border-right: 6px solid transparent;
                        border-top: 6px solid {{ $theme === 'primary' ? '#9333ea' : ($theme === 'success' ? '#059669' : '#374151') }};
                    @elseif($position === 'bottom')
                        border-left: 6px solid transparent;
                        border-right: 6px solid transparent;
                        border-bottom: 6px solid {{ $theme === 'primary' ? '#9333ea' : ($theme === 'success' ? '#059669' : '#374151') }};
                    @elseif($position === 'left')
                        border-top: 6px solid transparent;
                        border-bottom: 6px solid transparent;
                        border-left: 6px solid {{ $theme === 'primary' ? '#9333ea' : ($theme === 'success' ? '#059669' : '#374151') }};
                    @elseif($position === 'right')
                        border-top: 6px solid transparent;
                        border-bottom: 6px solid transparent;
                        border-right: 6px solid {{ $theme === 'primary' ? '#9333ea' : ($theme === 'success' ? '#059669' : '#374151') }};
                    @endif
                 ">
            </div>
        @endif
    </div>
</div>