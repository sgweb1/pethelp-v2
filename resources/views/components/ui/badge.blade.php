@props([
    'variant' => 'primary',
    'size' => 'md',
    'pill' => false,
    'removable' => false
])

@php
$variants = [
    'primary' => 'bg-blue-100 text-blue-800',
    'secondary' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'danger' => 'bg-red-100 text-red-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'info' => 'bg-blue-100 text-blue-800',
    'light' => 'bg-gray-50 text-gray-600',
    'dark' => 'bg-gray-800 text-white',
    'notification' => 'bg-red-500 border-2 border-white dark:border-gray-800',
    'notification-red' => 'bg-red-500 text-white',
    'notification-green' => 'bg-green-500 text-white',
    'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
];

$sizes = [
    'xs' => 'px-2 py-0.5 text-xs',
    'sm' => 'px-2.5 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-1.5 text-base',
    'dot' => 'h-3 w-3',
    'icon' => 'h-5 w-5 text-xs flex items-center justify-center',
];

$classes = collect([
    'inline-flex items-center font-medium',
    ($pill || in_array($size, ['dot', 'icon'])) ? 'rounded-full' : 'rounded',
    $variants[$variant] ?? $variants['primary'],
    $sizes[$size] ?? $sizes['md'],
])->filter()->implode(' ');
@endphp

<span
    {{ $attributes->merge(['class' => $classes]) }}
    @if($removable)
        x-data="{ show: true }"
        x-show="show"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
    @endif
>
    {{ $slot }}

    @if($removable)
        <button
            @click="show = false"
            class="ml-1 inline-flex items-center justify-center h-4 w-4 rounded-full hover:bg-black hover:bg-opacity-10 focus:outline-none"
        >
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    @endif
</span>