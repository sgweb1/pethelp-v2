{{--
    Mobile Step Navigator for Pet Sitter Wizard

    Komponent nawigacji między krokami zoptymalizowany dla urządzeń mobilnych.
    Wyświetla się jako fixed bottom bar z przyciskami nawigacyjnymi.
--}}

@props([
    'currentStep' => 1,
    'maxSteps' => 12,
    'canGoNext' => true,
    'canGoPrevious' => true,
    'isLoading' => false,
    'nextLabel' => 'Dalej',
    'previousLabel' => 'Wstecz',
    'completeLabel' => 'Zakończ'
])

<div {{ $attributes->merge(['class' => 'mobile-step-navigator fixed bottom-0 left-0 right-0 z-40 lg:hidden']) }}>
    {{-- Background with blur effect --}}
    <div class="absolute inset-0 bg-white/95 backdrop-blur-md border-t border-gray-200"></div>

    {{-- Navigation Content --}}
    <div class="relative p-4 safe-area-bottom">
        {{-- Step Progress Mini Bar --}}
        <div class="flex items-center justify-center mb-3">
            <div class="flex items-center space-x-1">
                @for($i = 1; $i <= min($maxSteps, 8); $i++)
                    <div class="w-2 h-2 rounded-full transition-all duration-200
                                {{ $i <= $currentStep ? 'bg-emerald-500' : 'bg-gray-300' }}
                                {{ $i == $currentStep ? 'scale-125' : '' }}">
                    </div>
                @endfor
                @if($maxSteps > 8)
                    <span class="text-xs text-gray-500 ml-2">+{{ $maxSteps - 8 }}</span>
                @endif
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="flex items-center justify-between gap-4">
            {{-- Previous Button --}}
            <button
                wire:click="previousStep"
                @if(!$canGoPrevious || $currentStep <= 1) disabled @endif
                class="flex items-center justify-center px-4 py-3 rounded-xl font-medium transition-all duration-200
                       {{ $canGoPrevious && $currentStep > 1
                          ? 'bg-gray-100 text-gray-700 hover:bg-gray-200 active:scale-95'
                          : 'bg-gray-50 text-gray-400 cursor-not-allowed' }}
                       touch-target">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ $previousLabel }}
            </button>

            {{-- Step Counter --}}
            <div class="flex flex-col items-center min-w-0">
                <div class="text-xs text-gray-500 font-medium">
                    Krok {{ $currentStep }} z {{ $maxSteps }}
                </div>
                <div class="w-16 h-1 bg-gray-200 rounded-full mt-1 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-300"
                         style="width: {{ ($currentStep / $maxSteps) * 100 }}%"></div>
                </div>
            </div>

            {{-- Next/Complete Button --}}
            @if($currentStep < $maxSteps)
                <button
                    wire:click="nextStep"
                    :disabled="isLoading || !{{ $canGoNext ? 'true' : 'false' }}"
                    class="flex items-center justify-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl
                           transition-all duration-200 shadow-lg
                           {{ $canGoNext
                              ? 'hover:bg-emerald-700 active:scale-95'
                              : 'opacity-50 cursor-not-allowed' }}
                           touch-target relative overflow-hidden">

                    {{-- Loading state --}}
                    <div wire:loading wire:target="nextStep" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    {{-- Normal state --}}
                    <div wire:loading.remove wire:target="nextStep" class="flex items-center">
                        {{ $nextLabel }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </button>
            @else
                <button
                    wire:click="completeSitterRegistration"
                    :disabled="isLoading"
                    class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl
                           hover:from-emerald-700 hover:to-teal-700 active:scale-95
                           transition-all duration-200 shadow-lg touch-target relative overflow-hidden">

                    {{-- Loading state --}}
                    <div wire:loading wire:target="completeSitterRegistration" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    {{-- Normal state --}}
                    <div wire:loading.remove wire:target="completeSitterRegistration" class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $completeLabel }}
                    </div>
                </button>
            @endif
        </div>

        {{-- Quick Actions (if needed) --}}
        <div class="flex justify-center mt-3">
            <button
                wire:click="saveDraft"
                class="text-xs text-gray-500 hover:text-gray-700 transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Zapisz postęp
            </button>
        </div>
    </div>
</div>

<style>
/* Mobile step navigator styles */
.mobile-step-navigator {
    /* Safe area for devices with home indicator */
    padding-bottom: env(safe-area-inset-bottom);
}

.safe-area-bottom {
    padding-bottom: env(safe-area-inset-bottom);
}

/* Touch optimization */
.touch-target {
    min-height: 44px;
    min-width: 44px;
}

/* Dark mode support */
.dark .mobile-step-navigator .bg-white\/95 {
    background-color: rgb(17 24 39 / 0.95);
}

.dark .mobile-step-navigator .border-gray-200 {
    border-color: theme('colors.gray.700');
}

.dark .mobile-step-navigator .bg-gray-100 {
    background-color: theme('colors.gray.700');
}

.dark .mobile-step-navigator .bg-gray-200 {
    background-color: theme('colors.gray.600');
}

.dark .mobile-step-navigator .text-gray-700 {
    color: theme('colors.gray.200');
}

.dark .mobile-step-navigator .text-gray-500 {
    color: theme('colors.gray.400');
}

/* Landscape orientation adjustments */
@media (max-width: 767px) and (orientation: landscape) {
    .mobile-step-navigator {
        padding: 0.75rem 1rem;
    }

    .mobile-step-navigator .mb-3 {
        margin-bottom: 0.5rem;
    }

    .mobile-step-navigator .mt-3 {
        margin-top: 0.5rem;
    }
}

/* Animation for step changes */
.mobile-step-navigator .transition-step {
    animation: stepChange 0.3s ease-out;
}

@keyframes stepChange {
    0% {
        transform: translateY(10px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .mobile-step-navigator * {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
    }
}
</style>

{{-- JavaScript for enhanced mobile interactions --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mobileStepNavigator', () => ({
        lastTouchY: 0,
        isScrolling: false,

        init() {
            // Handle touch interactions for better mobile UX
            this.$el.addEventListener('touchstart', (e) => {
                this.lastTouchY = e.touches[0].clientY;
            });

            this.$el.addEventListener('touchmove', (e) => {
                const currentY = e.touches[0].clientY;
                const diff = this.lastTouchY - currentY;

                // If scrolling up/down, add class for styling
                if (Math.abs(diff) > 5) {
                    this.isScrolling = true;
                    if (this.scrollAnimationFrame) {
                        cancelAnimationFrame(this.scrollAnimationFrame);
                    }

                    // Użyj requestAnimationFrame zamiast setTimeout
                    const startTime = performance.now();
                    const checkScrollEnd = (currentTime) => {
                        if (currentTime - startTime >= 150) {
                            this.isScrolling = false;
                        } else {
                            this.scrollAnimationFrame = requestAnimationFrame(checkScrollEnd);
                        }
                    };
                    this.scrollAnimationFrame = requestAnimationFrame(checkScrollEnd);
                }
            });
        },

        // Add haptic feedback for supported devices
        hapticFeedback() {
            if ('vibrate' in navigator) {
                navigator.vibrate(10);
            }
        }
    }));
});
</script>