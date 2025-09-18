<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:text-gray-900 transition-colors duration-150">
        <span>{{ $availableLocales[$currentLocale]['flag'] }}</span>
        <span class="hidden sm:block">{{ $availableLocales[$currentLocale]['name'] }}</span>
        <svg class="w-4 h-4 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
            @foreach($availableLocales as $locale => $config)
                <button wire:click="switchLanguage('{{ $locale }}')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150 {{ $currentLocale === $locale ? 'bg-gray-50 font-medium' : '' }}">
                    <span class="mr-3">{{ $config['flag'] }}</span>
                    <span>{{ $config['name'] }}</span>
                    @if($currentLocale === $locale)
                        <svg class="w-4 h-4 ml-auto text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
