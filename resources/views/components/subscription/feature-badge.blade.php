@props([
    'feature',
    'showUpgrade' => true,
    'size' => 'sm'
])

@php
    $hasFeature = auth()->check() && auth()->user()->hasFeature($feature);
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1 text-sm',
        'md' => 'px-4 py-2 text-base'
    ];
@endphp

@if($hasFeature)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full font-medium ' . ($sizeClasses[$size] ?? $sizeClasses['sm'])]) }}>
        <x-icon name="heroicon-s-check" class="w-4 h-4 mr-1" />
        Premium
    </span>
@else
    <div class="inline-flex items-center">
        <span {{ $attributes->merge(['class' => 'inline-flex items-center bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-full font-medium ' . ($sizeClasses[$size] ?? $sizeClasses['sm'])]) }}>
            <x-icon name="heroicon-s-lock-closed" class="w-4 h-4 mr-1" />
            Premium
        </span>
        @if($showUpgrade)
            <a href="{{ route('subscription.plans') }}"
               class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                Aktualizuj
            </a>
        @endif
    </div>
@endif