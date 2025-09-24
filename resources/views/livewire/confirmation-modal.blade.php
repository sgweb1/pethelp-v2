<div>
    @if($show)
        <!-- Modal backdrop -->
        <div
            x-data="{ show: @entangle('show') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="$wire.close()"
        >
            <!-- Modal content -->
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <!-- Header -->
                <div class="flex items-start justify-between">
                    <h3 class="text-base font-medium text-gray-900">
                        {{ $title }}
                    </h3>
                    <button
                        wire:click="close"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="mt-4">
                    <p class="text-xs text-gray-600">
                        {{ $message }}
                    </p>
                </div>

                <!-- Footer -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button
                        wire:click="close"
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors"
                    >
                        {{ $cancelText }}
                    </button>
                    <button
                        wire:click="confirm"
                        class="px-4 py-2 text-white text-xs font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{ $confirmClass }}"
                    >
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>