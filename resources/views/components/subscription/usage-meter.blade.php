@props([
    'current' => 0,
    'limit' => null,
    'label' => 'Ogłoszenia',
    'showUpgrade' => true
])

@php
    $percentage = $limit ? min(($current / $limit) * 100, 100) : 0;
    $isNearLimit = $limit && $current >= ($limit * 0.8);
    $isAtLimit = $limit && $current >= $limit;
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4']) }}>
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $current }}{{ $limit ? '/' . $limit : '' }}
        </span>
    </div>

    @if($limit)
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-300 {{ $isAtLimit ? 'bg-red-500' : ($isNearLimit ? 'bg-yellow-500' : 'bg-green-500') }}"
                 style="width: {{ $percentage }}%"></div>
        </div>

        <div class="mt-2 flex items-center justify-between">
            @if($isAtLimit)
                <span class="text-sm text-red-600 dark:text-red-400 font-medium">
                    Osiągnięto limit
                </span>
            @elseif($isNearLimit)
                <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">
                    Zbliżasz się do limitu
                </span>
            @else
                <span class="text-sm text-green-600 dark:text-green-400">
                    {{ $limit - $current }} pozostało
                </span>
            @endif

            @if($showUpgrade && ($isAtLimit || $isNearLimit))
                <a href="{{ route('subscription.plans') }}"
                   class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                    Zwiększ limit
                </a>
            @endif
        </div>
    @else
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="h-2 rounded-full bg-green-500 w-full"></div>
        </div>
        <div class="mt-2">
            <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                <x-icon name="heroicon-s-infinity" class="w-4 h-4 inline mr-1" />
                Nielimitowane
            </span>
        </div>
    @endif
</div>