@props([
    'title' => '',
    'value' => '',
    'icon' => '',
    'color' => 'primary',
    'trend' => null,
    'description' => null
])

@php
$colorClasses = [
    'primary' => 'bg-primary-50 text-primary-600 border-primary-200',
    'success' => 'bg-success-50 text-success-600 border-success-200',
    'warning' => 'bg-warning-50 text-warning-600 border-warning-200',
    'danger' => 'bg-danger-50 text-danger-600 border-danger-200',
    'nature' => 'bg-nature-50 text-nature-600 border-nature-200',
    'warm' => 'bg-warm-50 text-warm-600 border-warm-200'
];

$cardClass = $colorClasses[$color] ?? $colorClasses['primary'];
@endphp

<div class="bg-white/95 backdrop-blur-md border {{ $cardClass }} rounded-2xl p-6 shadow-soft hover:shadow-medium transition-all duration-300">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <!-- Title -->
            <p class="text-sm font-medium text-gray-600 mb-1">{{ $title }}</p>

            <!-- Value -->
            <div class="flex items-baseline space-x-2">
                <h3 class="text-2xl font-bold text-gray-900">{{ $value }}</h3>

                @if($trend)
                    <span class="text-xs font-medium {{ $trend['direction'] === 'up' ? 'text-success-600' : 'text-danger-600' }}">
                        @if($trend['direction'] === 'up')
                            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7M17 17H7"></path>
                            </svg>
                        @else
                            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10M7 7h10"></path>
                            </svg>
                        @endif
                        {{ $trend['value'] }}
                    </span>
                @endif
            </div>

            @if($description)
                <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
            @endif
        </div>

        <!-- Icon -->
        @if($icon)
            <div class="w-12 h-12 rounded-xl {{ $cardClass }} flex items-center justify-center">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>