<div class="pet-sitter-wizard">
{{-- Pet Sitter Registration Wizard - Airbnb Inspired Design --}}

    @if(!$isActive)
        {{-- Trigger Button (when wizard is not active) --}}
        <div class="wizard-trigger-container">
            {{-- Hero Section - Become Pet Sitter Call to Action --}}
            <div class="bg-gradient-to-r from-emerald-500 via-teal-600 to-cyan-600 rounded-2xl p-8 text-white shadow-xl">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-4">
                            <div class="text-4xl mr-4">🐾</div>
                            <div>
                                <h2 class="text-2xl font-bold mb-2">Zostań Pet Sitterem!</h2>
                                <p class="text-emerald-100 text-lg">Zarabiaj pomagając właścicielom zwierząt</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm">Elastyczne godziny</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm">Dodatkowy dochód</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm">Praca z pupilami</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button wire:click="activateWizard"
                                    class="inline-flex items-center px-8 py-4 bg-white text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                <span class="mr-2">Rozpocznij rejestrację</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </button>
                            <p class="text-emerald-100 text-sm">Tylko 5-10 minut • Bezpłatna rejestracja</p>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <div class="text-6xl opacity-20">🐕‍🦺</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Global Loading States for activation --}}
        <div wire:loading.flex wire:target="activateWizard"
             class="fixed inset-0 z-50 items-center justify-center bg-white bg-opacity-75 backdrop-blur-sm">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Ładowanie...</p>
            </div>
        </div>
    @else
        {{-- Fullscreen Wizard Container V4 --}}
        <div class="fixed inset-0 z-50 bg-gray-50"
             x-data="petSitterWizard()"
             x-init="init()"
             @ai-debug.window="console.log('🧪 AI Debug:', $event.detail); alert('AI Debug: ' + $event.detail)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            {{-- Fixed Top Progress Bar --}}
            <div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-[999]">
                <div class="max-w-7xl mx-auto px-4 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">
                            Krok {{ $currentStep }} z {{ $maxSteps }}
                            <span class="text-gray-400 ml-2">•</span>
                            <span class="text-gray-600 ml-2">
                                @php
                                    $stepTitles = [
                                        1 => 'Rodzaje zwierząt',
                                        2 => 'Usługi',
                                        3 => 'Lokalizacja',
                                        4 => 'Dostępność',
                                        5 => 'Dom i ogród',
                                        6 => 'Motywacja',
                                        7 => 'Doświadczenie',
                                        8 => 'Zdjęcia',
                                        9 => 'Weryfikacja',
                                        10 => 'Cennik',
                                        11 => 'Finalizacja',
                                    ];
                                @endphp
                                {{ $stepTitles[$currentStep] ?? 'Krok ' . $currentStep }}
                            </span>
                        </span>
                        <span class="text-sm font-medium text-emerald-600">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-500 ease-out"
                             style="background: linear-gradient(135deg, #10b981, #06b6d4); width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Floating AI Panel Toggle Button with Tooltip --}}
            <div class="fixed bottom-24 right-4 z-[998] flex items-center gap-3">

                {{-- Speech Bubble Tooltip - zawsze z lewej strony --}}
                <div x-show="!$wire.showAIPanel"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     class="flex-shrink-0">

                    {{-- Tooltip content - dopasowany do wysokości przycisku --}}
                    <div @click="$wire.showAIPanel = true"
                         class="bg-white rounded-2xl shadow-2xl border-2 border-emerald-300 px-5 py-3 h-14 flex items-center relative cursor-pointer hover:border-emerald-400 hover:shadow-xl transition-all duration-200 hover:scale-105">
                        <p class="text-sm font-semibold text-gray-800 leading-tight whitespace-nowrap">
                            💡 Mam dla Ciebie wskazówki i ważne informacje!
                        </p>

                        {{-- Arrow pointing to button (right side) --}}
                        <div class="absolute left-full top-1/2 -translate-y-1/2">
                            <div class="w-0 h-0 border-t-8 border-t-transparent border-b-8 border-b-transparent border-l-8 border-l-emerald-300"></div>
                            <div class="w-0 h-0 border-t-7 border-t-transparent border-b-7 border-b-transparent border-l-7 border-l-white absolute -left-[7px] top-1/2 -translate-y-1/2"></div>
                        </div>

                        {{-- Pulsating effect --}}
                        <div class="absolute inset-0 rounded-2xl bg-emerald-200 opacity-20 animate-ping pointer-events-none"></div>
                    </div>
                </div>

                {{-- AI Button --}}
                <button @click="$wire.showAIPanel = !$wire.showAIPanel"
                        class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white rounded-full shadow-xl flex items-center justify-center hover:scale-110 transition-all duration-300 flex-shrink-0"
                        :class="$wire.showAIPanel ? 'bg-red-500 rotate-90' : 'animate-bounce'">
                    <span class="text-2xl" x-text="$wire.showAIPanel ? '✕' : '🤖'"></span>
                </button>
            </div>

            {{-- Main Content Area V4 --}}
            <div class="pt-24 pb-32 overflow-y-auto relative" style="height: calc(100vh - 88px);">
                {{-- Sidebar Stepper (Desktop Only) - Scrollable --}}
                <div class="hidden xl:flex fixed left-6 top-28 bottom-32 z-30 overflow-y-auto flex-col gap-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                    @php
                        $steps = [
                            1 => ['icon' => '🐕', 'title' => 'Rodzaje zwierząt'],
                            2 => ['icon' => '🛠️', 'title' => 'Usługi'],
                            3 => ['icon' => '📍', 'title' => 'Lokalizacja'],
                            4 => ['icon' => '📅', 'title' => 'Dostępność'],
                            5 => ['icon' => '🏠', 'title' => 'Dom i ogród'],
                            6 => ['icon' => '👋', 'title' => 'Motywacja'],
                            7 => ['icon' => '⭐', 'title' => 'Doświadczenie'],
                            8 => ['icon' => '📸', 'title' => 'Zdjęcia'],
                            9 => ['icon' => '✅', 'title' => 'Weryfikacja'],
                            10 => ['icon' => '💰', 'title' => 'Cennik'],
                            11 => ['icon' => '🎉', 'title' => 'Finalizacja'],
                        ];
                    @endphp

                    @foreach($steps as $stepNum => $step)
                        <div class="relative group cursor-pointer">
                            @if($currentStep == $stepNum)
                                {{-- Active Step --}}
                                <div class="w-14 h-14 rounded-full flex items-center justify-center shadow-xl transition-all duration-300"
                                     style="background: linear-gradient(135deg, #10b981, #06b6d4);">
                                    <span class="text-xl">{{ $step['icon'] }}</span>
                                </div>
                            @elseif($currentStep > $stepNum)
                                {{-- Completed Step --}}
                                <div class="w-14 h-14 rounded-full flex items-center justify-center bg-emerald-500 text-white shadow-lg transition-all duration-300">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @else
                                {{-- Pending Step --}}
                                <div class="w-14 h-14 rounded-full flex items-center justify-center bg-white border-2 border-gray-200 text-gray-400 shadow transition-all duration-300 hover:border-gray-300 hover:shadow-md">
                                    <span class="text-xl opacity-60">{{ $step['icon'] }}</span>
                                </div>
                            @endif

                            {{-- Tooltip on hover --}}
                            <div class="absolute left-full ml-4 top-1/2 -translate-y-1/2 px-4 py-2 rounded-xl text-sm whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[60] shadow-xl"
                                 style="background: linear-gradient(135deg, #10b981, #06b6d4);">
                                <span class="font-semibold text-white">{{ $step['title'] }}</span>
                                {{-- Arrow pointing left --}}
                                <div class="absolute right-full top-1/2 -translate-y-1/2 w-0 h-0 border-t-[8px] border-t-transparent border-b-[8px] border-b-transparent border-r-[8px]" style="border-right-color: #10b981;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <main>
                    {{-- Step-by-step content - zmapowane na nową kolejność --}}
                    @include('livewire.pet-sitter-wizard.steps.step-' . $this->getStepFileNumber())
                </main>
            </div>

            {{-- AI Panel V4 - zgodny z mockupem --}}
            @include('livewire.pet-sitter-wizard.ai-panel-v4')
            {{-- Fixed Bottom Footer Navigation V4 --}}
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-[999]">
                <div class="max-w-2xl mx-auto px-4 py-4">
                    <div class="flex items-center justify-between space-x-3">
                        {{-- Back Button --}}
                        <button wire:click="previousStep"
                                :disabled="$wire.currentStep === 1"
                                class="px-4 sm:px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all text-sm sm:text-base disabled:opacity-50 disabled:cursor-not-allowed">
                            ← Wstecz
                        </button>

                        {{-- Next/Complete Button with gradient --}}
                        @if($currentStep < $maxSteps)
                            <button wire:click="nextStep"
                                    style="background: linear-gradient(135deg, #10b981, #06b6d4);"
                                    class="flex-1 sm:flex-initial sm:px-8 py-3 text-white rounded-xl font-semibold transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                                <span wire:loading.remove wire:target="nextStep">Dalej →</span>
                                <span wire:loading wire:target="nextStep">Ładowanie...</span>
                            </button>
                        @else
                            <button wire:click="completeSitterRegistration"
                                    style="background: linear-gradient(135deg, #10b981, #06b6d4);"
                                    class="flex-1 sm:flex-initial sm:px-8 py-3 text-white rounded-xl font-semibold transition-all transform hover:scale-105 text-sm sm:text-base">
                                <span wire:loading.remove wire:target="completeSitterRegistration">Zakończ ✓</span>
                                <span wire:loading wire:target="completeSitterRegistration">Finalizowanie...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

                    {{-- Loading States - Tylko dla określonych akcji --}}
                    <div wire:loading.flex
                         wire:target="nextStep,previousStep,completeSitterRegistration,editMotivationWithAI,editExperienceWithAI,generateMotivationSuggestion,generateExperienceSuggestion,deactivateWizard"
                         class="fixed inset-0 z-50 items-center justify-center bg-white bg-opacity-75 backdrop-blur-sm">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Przetwarzanie...</p>
                        </div>
                    </div>
        </div>
    @endif

    {{-- Styles --}}
    <style>
        /* Ukryj scrollbar dla sidebar stepper, ale zachowaj funkcjonalność przewijania */
        .overflow-y-auto::-webkit-scrollbar {
            display: none;
        }
    </style>
</div>