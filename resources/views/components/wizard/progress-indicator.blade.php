{{--
    Responsive Progress Indicator for Pet Sitter Wizard

    Komponent wyświetlający postęp w wizardzie z różnymi trybami:
    - Mobile: kompaktowe dots/procenty
    - Tablet: mini step labels
    - Desktop: pełne step navigation
--}}

@props([
    'currentStep' => 1,
    'maxSteps' => 12,
    'completedSteps' => [],
    'stepTitles' => [],
    'showLabels' => true,
    'interactive' => true,
    'size' => 'md' // sm, md, lg
])

@php
$progressPercentage = ($currentStep / $maxSteps) * 100;

// Domyślne tytuły kroków jeśli nie podano
$defaultStepTitles = [
    1 => 'Motywacja',
    2 => 'Dane osobowe',
    3 => 'Doświadczenie',
    4 => 'Usługi',
    5 => 'Lokalizacja',
    6 => 'Dostępność',
    7 => 'Środowisko',
    8 => 'Zdjęcia',
    9 => 'Weryfikacja',
    10 => 'Cennik',
    11 => 'Regulamin',
    12 => 'Podgląd'
];

$stepTitles = array_merge($defaultStepTitles, $stepTitles);

$sizeClasses = [
    'sm' => [
        'dot' => 'w-2 h-2 sm:w-2.5 sm:h-2.5',
        'text' => 'text-xs',
        'spacing' => 'gap-1 sm:gap-2'
    ],
    'md' => [
        'dot' => 'w-2.5 h-2.5 sm:w-3 sm:h-3',
        'text' => 'text-xs sm:text-sm',
        'spacing' => 'gap-2 sm:gap-3'
    ],
    'lg' => [
        'dot' => 'w-3 h-3 sm:w-4 sm:h-4',
        'text' => 'text-sm sm:text-base',
        'spacing' => 'gap-2 sm:gap-4'
    ]
];

$classes = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'wizard-progress-indicator']) }}>
    {{-- Mobile: Progress Bar + Current Step Info --}}
    <div class="block sm:hidden">
        {{-- Progress Bar --}}
        <div class="wizard-progress mb-3">
            <div class="wizard-progress-bar" style="width: {{ $progressPercentage }}%"></div>
        </div>

        {{-- Current Step Info --}}
        <div class="flex items-center justify-between text-xs">
            <span class="font-medium text-gray-900">
                {{ $stepTitles[$currentStep] ?? "Krok {$currentStep}" }}
            </span>
            <span class="text-gray-500">
                {{ $currentStep }} / {{ $maxSteps }}
            </span>
        </div>
    </div>

    {{-- Tablet: Compact Dots with Mini Labels --}}
    <div class="hidden sm:block lg:hidden">
        {{-- Progress Bar --}}
        <div class="wizard-progress mb-4">
            <div class="wizard-progress-bar" style="width: {{ $progressPercentage }}%"></div>
        </div>

        {{-- Dot Navigation --}}
        <div class="flex justify-center {{ $classes['spacing'] }} mb-2">
            @for($i = 1; $i <= $maxSteps; $i++)
                @php
                $isActive = $i == $currentStep;
                $isCompleted = in_array($i, $completedSteps) || $i < $currentStep;
                $isAccessible = $i <= $currentStep;
                @endphp

                <button
                    @if($interactive && $isAccessible)
                        wire:click="goToStep({{ $i }})"
                    @else
                        disabled
                    @endif
                    class="wizard-step-dot {{ $classes['dot'] }}
                           {{ $isAccessible ? 'cursor-pointer hover:scale-110' : 'cursor-not-allowed' }}
                           transition-all duration-200"
                    data-active="{{ $isActive ? 'true' : 'false' }}"
                    data-completed="{{ $isCompleted ? 'true' : 'false' }}"
                    title="{{ $stepTitles[$i] ?? "Krok {$i}" }}">
                </button>
            @endfor
        </div>

        {{-- Current Step Label --}}
        <div class="text-center {{ $classes['text'] }} text-gray-600">
            {{ $stepTitles[$currentStep] ?? "Krok {$currentStep}" }}
        </div>
    </div>

    {{-- Desktop: Full Step Navigation with Labels --}}
    <div class="hidden lg:block">
        {{-- Progress Bar --}}
        <div class="wizard-progress mb-6">
            <div class="wizard-progress-bar" style="width: {{ $progressPercentage }}%"></div>
        </div>

        {{-- Step Navigation --}}
        <div class="flex justify-center items-center gap-1">
            @for($i = 1; $i <= $maxSteps; $i++)
                @php
                $isActive = $i == $currentStep;
                $isCompleted = in_array($i, $completedSteps) || $i < $currentStep;
                $isAccessible = $i <= $currentStep;
                @endphp

                {{-- Step Button --}}
                <div class="flex flex-col items-center">
                    <button
                        @if($interactive && $isAccessible)
                            wire:click="goToStep({{ $i }})"
                        @else
                            disabled
                        @endif
                        class="wizard-step-dot {{ $classes['dot'] }} mb-2
                               {{ $isAccessible ? 'cursor-pointer hover:scale-110' : 'cursor-not-allowed' }}
                               transition-all duration-200"
                        data-active="{{ $isActive ? 'true' : 'false' }}"
                        data-completed="{{ $isCompleted ? 'true' : 'false' }}">
                    </button>

                    @if($showLabels && isset($stepTitles[$i]))
                        <span class="{{ $classes['text'] }}
                                     {{ $isActive ? 'text-emerald-600 font-medium' :
                                        ($isCompleted ? 'text-emerald-500' : 'text-gray-400') }}
                                     transition-colors duration-200 text-center max-w-[4rem]">
                            {{ $stepTitles[$i] }}
                        </span>
                    @endif
                </div>

                {{-- Connector Line (except for last step) --}}
                @if($i < $maxSteps)
                    <div class="flex-1 h-px bg-gray-300 mx-2 mb-{{ $showLabels ? '6' : '0' }}
                                {{ $i < $currentStep ? 'bg-emerald-400' : '' }}
                                transition-colors duration-300"></div>
                @endif
            @endfor
        </div>
    </div>
</div>

<style>
/* Responsive progress indicator styles */
.wizard-progress-indicator .wizard-step-dot {
    position: relative;
    border-radius: 50%;
    background-color: theme('colors.gray.300');
    transition: all 0.2s ease;
}

.wizard-progress-indicator .wizard-step-dot[data-active="true"] {
    background-color: theme('colors.emerald.500');
    transform: scale(1.2);
    box-shadow: 0 0 0 3px theme('colors.emerald.100');
}

.wizard-progress-indicator .wizard-step-dot[data-completed="true"] {
    background-color: theme('colors.emerald.400');
}

.wizard-progress-indicator .wizard-step-dot:disabled {
    opacity: 0.5;
}

/* Dark mode support */
.dark .wizard-progress-indicator .wizard-step-dot {
    background-color: theme('colors.gray.600');
}

.dark .wizard-progress-indicator .wizard-step-dot[data-active="true"] {
    background-color: theme('colors.emerald.500');
    box-shadow: 0 0 0 3px theme('colors.emerald.900');
}

.dark .wizard-progress-indicator .wizard-step-dot[data-completed="true"] {
    background-color: theme('colors.emerald.400');
}

/* Loading animation for active step */
.wizard-progress-indicator .wizard-step-dot[data-active="true"]::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 50%;
    background: conic-gradient(from 0deg, theme('colors.emerald.500'), theme('colors.emerald.300'), theme('colors.emerald.500'));
    z-index: -1;
    animation: rotate 2s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Hover effects */
.wizard-progress-indicator .wizard-step-dot:hover:not(:disabled) {
    transform: scale(1.1);
}

.wizard-progress-indicator .wizard-step-dot[data-active="true"]:hover {
    transform: scale(1.25);
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .wizard-progress-indicator .wizard-step-dot,
    .wizard-progress-indicator .wizard-step-dot::after {
        animation: none;
        transition: none;
    }
}
</style>