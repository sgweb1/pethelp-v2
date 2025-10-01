{{--
    Bezpieczny komponent ikony SVG używający SVG.js

    @param string $icon - Nazwa ikony (loading, check, close, arrowRight, arrowLeft, location, settings)
    @param string $size - Rozmiar w formacie "WxH" (np. "16x16", "20x20")
    @param string $classes - Klasy CSS do zastosowania
    @param array $attributes - Dodatkowe atrybuty HTML

    @example
    <x-ui.safe-icon icon="loading" size="16x16" classes="text-white animate-spin" />
    <x-ui.safe-icon icon="check" size="20x20" classes="text-green-500" />
--}}

@props([
    'icon',
    'size' => '20x20',
    'classes' => '',
    'attributes' => []
])

<div
    data-svg-icon="{{ $icon }}"
    data-svg-size="{{ $size }}"
    @if($classes) data-svg-classes="{{ $classes }}" @endif
    {{ $attributes->merge(['class' => 'inline-block']) }}
></div>