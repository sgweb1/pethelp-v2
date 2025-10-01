<div class="max-w-2xl mx-auto px-4">
{{-- Krok 3: Rodzaje zwierząt - Wybór rodzajów i rozmiarów zwierząt, którymi będzie się zajmować opiekun --}}

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Jakimi zwierzętami chcesz się zajmować?</h1>
        <p class="text-gray-600 text-lg">Wybierz rodzaje i rozmiary zwierząt, z którymi czujesz się komfortowo.</p>
    </div>

    <div class="space-y-6">

        {{-- Animal Types Card - dynamiczne z bazy danych --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <label class="block text-sm font-semibold text-gray-900 mb-4">
                Rodzaje zwierząt <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($this->formattedAnimalTypes as $value => $info)
                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.02] @if(in_array($value, $animalTypes)) border-emerald-500 bg-white text-gray-900 @else bg-white border-gray-200 hover:border-gray-300 text-gray-900 @endif"
                           wire:click.prevent="toggleAnimalType('{{ $value }}')">
                        <input type="checkbox"
                               value="{{ $value }}"
                               class="sr-only">
                        <span class="text-2xl mr-3">{{ $info[1] }}</span>
                        <span class="flex-1 font-medium text-sm">{{ $info[0] }}</span>
                        @if(in_array($value, $animalTypes))
                            <span class="text-emerald-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>
            @error('animalTypes')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
            <p class="mt-4 text-xs text-gray-500 italic">
                💡 Dostępne typy zwierząt są pobierane z bazy danych.
            </p>
        </div>

        {{-- Animal Sizes Card (only show if dogs or cats are selected) --}}
        @if(in_array('dogs', $animalTypes) || in_array('cats', $animalTypes))
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <label class="block text-sm font-semibold text-gray-900 mb-4">
                Rozmiary zwierząt <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach([
                    'small' => ['Małe (do 10kg)', '🐕‍🦺'],
                    'medium' => ['Średnie (10-25kg)', '🐕'],
                    'large' => ['Duże (25kg+)', '🐕‍🦮']
                ] as $value => $info)
                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.02] @if(in_array($value, $animalSizes)) border-emerald-500 bg-white text-gray-900 @else bg-white border-gray-200 hover:border-gray-300 text-gray-900 @endif"
                           wire:click.prevent="toggleAnimalSize('{{ $value }}')">
                        <input type="checkbox"
                                value="{{ $value }}"
                                class="sr-only">
                        <span class="text-2xl mr-3">{{ $info[1] }}</span>
                        <span class="flex-1 font-medium text-sm">{{ $info[0] }}</span>
                        @if(in_array($value, $animalSizes))
                            <span class="text-emerald-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>
            @error('animalSizes')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
        </div>
        @endif

    </div>
</div>