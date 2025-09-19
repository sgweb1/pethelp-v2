@props([
    'id' => null,
    'title' => '',
    'size' => 'md',
    'closeable' => true,
    'show' => false
])

@php
$modalId = $id ?? 'modal-' . uniqid();

$sizeClasses = match($size) {
    'sm' => 'max-w-md',
    'md' => 'max-w-lg',
    'lg' => 'max-w-4xl',
    'xl' => 'max-w-6xl',
    'full' => 'max-w-full m-4',
    default => 'max-w-lg'
};
@endphp

<div
    id="{{ $modalId }}"
    class="modal-accessible"
    aria-hidden="{{ $show ? 'false' : 'true' }}"
    aria-labelledby="{{ $modalId }}-title"
    aria-describedby="{{ $modalId }}-content"
    role="dialog"
    aria-modal="true"
    style="display: {{ $show ? 'flex' : 'none' }}"
    {{ $attributes }}
>
    <div class="modal-content-accessible {{ $sizeClasses }} w-full">
        @if($title)
            <header class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <h2 id="{{ $modalId }}-title" class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h2>
                @if($closeable)
                    <button
                        type="button"
                        class="modal-close text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded p-1"
                        aria-label="Zamknij modal"
                        onclick="accessibilityHelper.closeModal(document.getElementById('{{ $modalId }}'))"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </header>
        @endif

        <div id="{{ $modalId }}-content" class="modal-body">
            {{ $slot }}
        </div>

        @isset($footer)
            <footer class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                {{ $footer }}
            </footer>
        @endisset
    </div>
</div>

@if($closeable)
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('{{ $modalId }}');
                if (modal && modal.getAttribute('aria-hidden') === 'false') {
                    accessibilityHelper.closeModal(modal);
                }
            }
        });

        // Close modal when clicking backdrop
        document.getElementById('{{ $modalId }}').addEventListener('click', function(e) {
            if (e.target === this) {
                accessibilityHelper.closeModal(this);
            }
        });
    </script>
@endif