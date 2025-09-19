{{-- Process Step Component --}}
@props(['step', 'color', 'title', 'description'])

@php
$colorClasses = [
    'blue' => 'from-blue-500 to-blue-600',
    'purple' => 'from-purple-500 to-purple-600',
    'green' => 'from-green-500 to-green-600',
];
$gradientClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="text-center group">
    <div class="w-16 h-16 bg-gradient-to-r {{ $gradientClass }} rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
        {{ $step }}
    </div>
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        {{ $title }}
    </h3>
    <p class="text-gray-600 dark:text-gray-300">
        {{ $description }}
    </p>
</div>