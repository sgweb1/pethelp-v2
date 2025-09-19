{{-- Service Card Component --}}
@props(['icon', 'color', 'title', 'description'])

@php
$colorClasses = [
    'blue' => 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400',
    'green' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400',
    'purple' => 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400',
    'orange' => 'bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-400',
];
$iconColorClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white dark:bg-gray-900 rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
    <div class="w-16 h-16 {{ $iconColorClass }} rounded-full flex items-center justify-center mx-auto mb-4">
        <x-icon name="{{ $icon }}" class="w-8 h-8" />
    </div>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $title }}</h3>
    <p class="text-gray-600 dark:text-gray-300 text-sm">
        {{ $description }}
    </p>
</div>