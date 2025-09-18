@props([
    'id' => 'modal',
    'size' => 'md',
    'title' => null,
    'footer' => null,
    'backdrop' => true,
    'keyboard' => true,
    'static' => false
])

@php
$sizes = [
    'xs' => 'max-w-xs',
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
    'full' => 'max-w-full mx-4',
];

$modalSize = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{
        show: false,
        open() { this.show = true; document.body.style.overflow = 'hidden'; },
        close() { this.show = false; document.body.style.overflow = 'auto'; }
    }"
    x-show="show"
    x-on:keydown.escape.window="{{ $keyboard && !$static ? 'close()' : '' }}"
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open()"
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') close()"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @if($backdrop && !$static) @click="close()" @endif
    ></div>

    <!-- Modal -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0"
    >
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $modalSize }} sm:w-full">
            <!-- Header -->
            @if($title)
                <div class="bg-white px-4 py-3 border-b border-gray-200 sm:px-6 flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $title }}
                    </h3>
                    <button
                        @click="close()"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Body -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if($footer)
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Helper functions for opening/closing modals -->
@push('scripts')
<script>
    function openModal(id) {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id } }));
    }

    function closeModal(id) {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { id } }));
    }
</script>
@endpush