@props([
    'size' => 'md',
    'position' => 'relative',
    'showLabel' => true,
    'showModeOptions' => false
])

@php
$sizeClasses = match($size) {
    'sm' => 'w-8 h-8',
    'md' => 'w-10 h-10',
    'lg' => 'w-12 h-12',
    default => 'w-10 h-10'
};

$iconSize = match($size) {
    'sm' => 'w-4 h-4',
    'md' => 'w-5 h-5',
    'lg' => 'w-6 h-6',
    default => 'w-5 h-5'
};

$positionClasses = match($position) {
    'fixed' => 'fixed top-4 right-4 z-50',
    'absolute' => 'absolute top-4 right-4 z-30',
    'relative' => 'relative',
    default => 'relative'
};
@endphp

<div {{ $attributes->merge(['class' => "dark-mode-toggle {$positionClasses}"]) }}
     x-data="darkModeToggle"
     x-init="init()"
     @dark-mode-changed.window="updateState($event.detail)">

    @if($showModeOptions)
        <!-- Extended mode selector -->
        <div class="flex items-center gap-2">
            @if($showLabel)
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:inline">
                    Motyw:
                </span>
            @endif

            <div class="relative">
                <button @click="showOptions = !showOptions"
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
                               bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               hover:bg-gray-200 dark:hover:bg-gray-600"
                        :class="{ 'ring-2 ring-blue-500': showOptions }">
                    <template x-if="currentMode === 'light'">
                        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </template>
                    <template x-if="currentMode === 'dark'">
                        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </template>
                    <template x-if="currentMode === 'auto'">
                        <svg class="{{ $iconSize }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </template>

                    <span class="hidden sm:inline" x-text="getModeLabel(currentMode)"></span>

                    <svg class="w-4 h-4 transition-transform duration-200"
                         :class="{ 'rotate-180': showOptions }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown options -->
                <div x-show="showOptions"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="showOptions = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">

                    <div class="py-1">
                        <button @click="setMode('light')"
                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="{ 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': currentMode === 'light' }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Jasny</span>
                            <svg x-show="currentMode === 'light'" class="w-4 h-4 ml-auto text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <button @click="setMode('dark')"
                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="{ 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': currentMode === 'dark' }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <span>Ciemny</span>
                            <svg x-show="currentMode === 'dark'" class="w-4 h-4 ml-auto text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <button @click="setMode('auto')"
                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="{ 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': currentMode === 'auto' }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Automatyczny</span>
                            <svg x-show="currentMode === 'auto'" class="w-4 h-4 ml-auto text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    @if($currentMode === 'auto')
                        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-show="systemPreference">System preferuje ciemny motyw</span>
                                <span x-show="!systemPreference">System preferuje jasny motyw</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Simple toggle button -->
        <button @click="toggle()"
                class="{{ $sizeClasses }} flex items-center justify-center rounded-lg transition-all duration-200
                       bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                       hover:bg-gray-200 dark:hover:bg-gray-600 hover:scale-105 active:scale-95
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800"
                :title="isDark ? 'Przełącz na jasny motyw' : 'Przełącz na ciemny motyw'"
                aria-label="Przełącz motyw">

            <!-- Light mode icon -->
            <svg x-show="!isDark"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="opacity-0 rotate-90 scale-50"
                 x-transition:enter-end="opacity-100 rotate-0 scale-100"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="opacity-100 rotate-0 scale-100"
                 x-transition:leave-end="opacity-0 -rotate-90 scale-50"
                 class="{{ $iconSize }}"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>

            <!-- Dark mode icon -->
            <svg x-show="isDark"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="opacity-0 -rotate-90 scale-50"
                 x-transition:enter-end="opacity-100 rotate-0 scale-100"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="opacity-100 rotate-0 scale-100"
                 x-transition:leave-end="opacity-0 rotate-90 scale-50"
                 class="{{ $iconSize }}"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        @if($showLabel)
            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:inline">
                <span x-show="isDark">Ciemny motyw</span>
                <span x-show="!isDark">Jasny motyw</span>
            </span>
        @endif
    @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('darkModeToggle', () => ({
        isDark: false,
        currentMode: 'auto',
        systemPreference: false,
        showOptions: false,

        init() {
            this.updateState({
                isDark: window.darkMode?.isDark() || false,
                mode: window.darkMode?.mode() || 'auto',
                systemPreference: window.darkMode?.systemPrefers() || false
            });
        },

        updateState(detail) {
            this.isDark = detail.isDark;
            this.currentMode = detail.mode;
            this.systemPreference = detail.systemPreference;
        },

        toggle() {
            if (window.darkMode) {
                window.darkMode.toggle();
            }
            this.showOptions = false;
        },

        setMode(mode) {
            if (window.darkMode) {
                window.darkMode.setMode(mode);
            }
            this.showOptions = false;
        },

        getModeLabel(mode) {
            switch(mode) {
                case 'light': return 'Jasny';
                case 'dark': return 'Ciemny';
                case 'auto': return 'Auto';
                default: return 'Auto';
            }
        }
    }));
});
</script>