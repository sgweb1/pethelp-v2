@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false
])

@php
$variants = [
    'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-soft hover:shadow-medium',
    'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500 shadow-soft hover:shadow-medium',
    'success' => 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500 shadow-soft',
    'warning' => 'bg-warning-500 text-white hover:bg-warning-600 focus:ring-warning-500 shadow-soft',
    'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500 shadow-soft',
    'info' => 'bg-info-600 text-white hover:bg-info-700 focus:ring-info-500 shadow-soft',
    'warm' => 'bg-warm-500 text-white hover:bg-warm-600 focus:ring-warm-500 shadow-soft',
    'nature' => 'bg-nature-500 text-white hover:bg-nature-600 focus:ring-nature-500 shadow-soft',
    'outline' => 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500',
    'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-500',
    'link' => 'text-primary-600 hover:text-primary-700 underline-offset-4 hover:underline p-0 h-auto',
];

$sizes = [
    'xs' => 'px-2 py-1 text-xs h-7',
    'sm' => 'px-3 py-1.5 text-sm h-8',
    'md' => 'px-4 py-2 text-sm h-10',
    'lg' => 'px-6 py-3 text-base h-12',
    'xl' => 'px-8 py-4 text-lg h-14',
];

$baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
$widthClass = $fullWidth ? 'w-full' : '';

$classes = collect([
    $baseClasses,
    $variants[$variant] ?? $variants['primary'],
    $sizes[$size] ?? $sizes['md'],
    $widthClass,
])->filter()->implode(' ');
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $classes]) }}
    @if($loading) x-data="{ loading: true }" x-bind:disabled="loading" @endif
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Ładowanie...
    @else
        @if($icon && $iconPosition === 'left')
            <span class="mr-2">{!! $icon !!}</span>
        @endif

        {{ $slot }}

        @if($icon && $iconPosition === 'right')
            <span class="ml-2">{!! $icon !!}</span>
        @endif
    @endif
</button>