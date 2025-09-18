@props([
    'header' => null,
    'footer' => null,
    'variant' => 'default',
    'shadow' => true,
    'padding' => true
])

@php
$variants = [
    'default' => 'bg-white border border-gray-200',
    'primary' => 'bg-blue-50 border border-blue-200',
    'success' => 'bg-green-50 border border-green-200',
    'warning' => 'bg-yellow-50 border border-yellow-200',
    'danger' => 'bg-red-50 border border-red-200',
    'dark' => 'bg-gray-800 border border-gray-700 text-white',
];

$classes = collect([
    'rounded-lg overflow-hidden',
    $shadow ? 'shadow-lg' : '',
    $variants[$variant] ?? $variants['default'],
])->filter()->implode(' ');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($header)
        <div class="px-6 py-4 border-b {{ $variant === 'dark' ? 'border-gray-700' : 'border-gray-200' }}">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-4 border-t {{ $variant === 'dark' ? 'border-gray-700' : 'border-gray-200' }} bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>