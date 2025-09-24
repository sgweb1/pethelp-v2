@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => true,
    'title' => null,
    'timeout' => null, // auto-dismiss after X seconds
    'actions' => false // show custom action buttons
])

@php
$types = [
    'primary' => [
        'class' => 'bg-blue-50 border-blue-200 text-blue-800',
        'icon' => '🔵'
    ],
    'secondary' => [
        'class' => 'bg-gray-50 border-gray-200 text-gray-800',
        'icon' => '⚪'
    ],
    'success' => [
        'class' => 'bg-green-50 border-green-200 text-green-800',
        'icon' => '✅'
    ],
    'danger' => [
        'class' => 'bg-red-50 border-red-200 text-red-800',
        'icon' => '❌'
    ],
    'warning' => [
        'class' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'icon' => '⚠️'
    ],
    'info' => [
        'class' => 'bg-blue-50 border-blue-200 text-blue-800',
        'icon' => 'ℹ️'
    ],
    'light' => [
        'class' => 'bg-white border-gray-200 text-gray-800',
        'icon' => '⚪'
    ],
    'dark' => [
        'class' => 'bg-gray-800 border-gray-700 text-white',
        'icon' => '⚫'
    ],
];

$config = $types[$type] ?? $types['info'];
$classes = collect([
    'border rounded-lg p-4 transition-all duration-300',
    $config['class'],
    $dismissible || $timeout ? 'relative' : '',
])->filter()->implode(' ');
@endphp

<div
    {{ $attributes->merge(['class' => $classes]) }}
    @if($dismissible || $timeout)
        x-data="{
            show: true,
            init() {
                @if($timeout)
                    setTimeout(() => { this.show = false }, {{ $timeout * 1000 }});
                @endif
            }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
    @endif
>
    <div class="flex items-start">
        @if($icon)
            <div class="flex-shrink-0 mr-3">
                <span class="text-base">{{ $config['icon'] }}</span>
            </div>
        @endif

        <div class="flex-1">
            @if($title)
                <h4 class="font-semibold mb-1 text-sm">{{ $title }}</h4>
            @endif

            <div>{{ $slot }}</div>

            @if($actions)
                <div class="mt-3 flex space-x-2">
                    {{ $actions }}
                </div>
            @endif
        </div>

        @if($dismissible)
            <button
                @click="show = false"
                class="ml-4 flex-shrink-0 text-current opacity-70 hover:opacity-100 transition-opacity"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        @endif
    </div>
</div>