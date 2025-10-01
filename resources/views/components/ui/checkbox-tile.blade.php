{{--
    Uniwersalny komponent kafelka checkbox

    Wyświetla interaktywny kafelek z checkboxem, ikoną i tekstem.
    Może być używany zarówno z Livewire jak i Alpine.js.

    @param string $value - Wartość checkbox
    @param string $label - Tekst wyświetlany na kafelku
    @param string $icon - Ikona emoji lub SVG
    @param bool $checked - Czy checkbox jest zaznaczony
    @param string $wireClick - Metoda Livewire do wywołania (opcjonalne)
    @param string $alpineClick - Metoda Alpine.js do wywołania (opcjonalne)
    @param string $alpineChecked - Wyrażenie Alpine.js sprawdzające stan (opcjonalne)
--}}

@props([
    'value' => '',
    'label' => '',
    'description' => '',
    'icon' => '',
    'checked' => false,
    'wireClick' => null,
    'alpineClick' => null,
    'alpineChecked' => null
])

@if($alpineChecked)
    {{-- Alpine.js version --}}
    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors duration-200"
           :class="{{ $alpineChecked }} ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300'"
           @if($alpineClick) @click="{{ $alpineClick }}('{{ $value }}')" @endif>

        <input type="checkbox"
               value="{{ $value }}"
               class="sr-only"
               :checked="{{ $alpineChecked }}">

        <div class="flex items-center w-full">
            @if($icon)
                <span class="text-2xl mr-3">{{ $icon }}</span>
            @endif

            <div class="flex-1 min-w-0">
                <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                @if($description)
                    <div class="wizard-checkbox-description">{{ $description }}</div>
                @endif
            </div>

            {{-- Checkmark icon --}}
            <svg class="w-5 h-5 ml-auto text-emerald-600 transition-opacity duration-200"
                 :class="{{ $alpineChecked }} ? 'opacity-100' : 'opacity-0'"
                 fill="currentColor"
                 viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>
    </label>
@else
    {{-- Livewire version --}}
    <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors duration-200 {{ $checked ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300' }}"
           @if($wireClick) wire:click="{{ $wireClick }}('{{ $value }}')" @endif>

        <input type="checkbox"
               value="{{ $value }}"
               class="sr-only"
               @checked($checked)>

        <div class="flex items-center w-full">
            @if($icon)
                <span class="text-2xl mr-3">{{ $icon }}</span>
            @endif

            <div class="flex-1 min-w-0">
                <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                @if($description)
                    <div class="wizard-checkbox-description">{{ $description }}</div>
                @endif
            </div>

            {{-- Checkmark icon --}}
            <svg class="w-5 h-5 ml-auto text-emerald-600 transition-opacity duration-200 {{ $checked ? 'opacity-100' : 'opacity-0' }}"
                 fill="currentColor"
                 viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>
    </label>
@endif