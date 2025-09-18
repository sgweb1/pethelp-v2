@props([
    'trigger' => null,
    'position' => 'bottom-left',
    'width' => 'w-48'
])

@php
$positions = [
    'top-left' => 'bottom-full left-0 mb-1',
    'top-right' => 'bottom-full right-0 mb-1',
    'bottom-left' => 'top-full left-0 mt-1',
    'bottom-right' => 'top-full right-0 mt-1',
    'left' => 'right-full top-0 mr-1',
    'right' => 'left-full top-0 ml-1',
];

$positionClass = $positions[$position] ?? $positions['bottom-left'];
@endphp

<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative inline-block text-left"
>
    <!-- Trigger -->
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute {{ $positionClass }} {{ $width }} z-50"
        style="display: none;"
    >
        <div class="bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none">
            {{ $slot }}
        </div>
    </div>
</div>