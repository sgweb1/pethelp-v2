@props([
    'items' => [],
    'multiple' => false,
    'flush' => false
])

@php
$accordionClasses = collect([
    'space-y-2',
    $flush ? 'border-0 rounded-none' : 'border border-gray-200 rounded-lg overflow-hidden',
])->filter()->implode(' ');
@endphp

<div
    x-data="{
        activeItems: {{ $multiple ? '[]' : 'null' }},
        toggle(index) {
            if ({{ $multiple ? 'true' : 'false' }}) {
                if (this.activeItems.includes(index)) {
                    this.activeItems = this.activeItems.filter(i => i !== index);
                } else {
                    this.activeItems.push(index);
                }
            } else {
                this.activeItems = this.activeItems === index ? null : index;
            }
        },
        isActive(index) {
            return {{ $multiple ? 'true' : 'false' }} ? this.activeItems.includes(index) : this.activeItems === index;
        }
    }"
    {{ $attributes->merge(['class' => $accordionClasses]) }}
>
    @foreach($items as $index => $item)
        <div class="{{ $flush ? 'border-b border-gray-200 last:border-b-0' : 'border-b border-gray-200 last:border-b-0' }}">
            <!-- Header -->
            <button
                @click="toggle({{ $index }})"
                class="w-full px-4 py-3 text-left bg-white hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors duration-200 flex items-center justify-between"
                :class="{ 'bg-gray-50': isActive({{ $index }}) }"
            >
                <span class="font-medium text-gray-900">{{ $item['title'] }}</span>
                <svg
                    class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                    :class="{ 'rotate-180': isActive({{ $index }}) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Content -->
            <div
                x-show="isActive({{ $index }})"
                x-collapse
                class="bg-white"
            >
                <div class="px-4 py-3 text-gray-700 border-t border-gray-200">
                    {!! $item['content'] !!}
                </div>
            </div>
        </div>
    @endforeach

    <!-- Custom slot content -->
    {{ $slot }}
</div>