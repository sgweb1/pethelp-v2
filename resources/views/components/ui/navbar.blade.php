@props([
    'brand' => null,
    'variant' => 'light',
    'fixed' => false,
    'container' => true
])

@php
$variants = [
    'light' => 'bg-white border-gray-200 text-gray-900',
    'dark' => 'bg-gray-800 border-gray-700 text-white',
    'primary' => 'bg-blue-600 text-white',
    'transparent' => 'bg-transparent',
];

$classes = collect([
    'border-b shadow-sm',
    $fixed ? 'fixed top-0 left-0 right-0 z-40' : '',
    $variants[$variant] ?? $variants['light'],
])->filter()->implode(' ');
@endphp

<nav {{ $attributes->merge(['class' => $classes]) }}>
    <div class="{{ $container ? 'container mx-auto px-4' : 'px-4' }}">
        <div class="flex items-center justify-between h-16">
            <!-- Brand -->
            @if($brand)
                <div class="flex-shrink-0">
                    {{ $brand }}
                </div>
            @endif

            <!-- Desktop Menu -->
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    {{ $slot }}
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button
                    x-data
                    @click="$dispatch('toggle-mobile-menu')"
                    class="inline-flex items-center justify-center p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            x-data="{ open: false }"
            @toggle-mobile-menu.window="open = !open"
            @click.outside="open = false"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="md:hidden"
            style="display: none;"
        >
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200">
                {{ $mobileMenu ?? $slot }}
            </div>
        </div>
    </div>
</nav>