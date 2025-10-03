{{-- Krok 4: Wybór usług --}}
<div class="max-w-2xl mx-auto px-4">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Jakie usługi chcesz oferować?</h1>
        <p class="text-gray-600 text-lg">Wybierz rodzaje opieki, które możesz zapewnić zwierzętom</p>
    </div>

    <div class="space-y-6">
        {{-- Wszystkie usługi Card - dynamiczne z bazy danych --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6">
            <label class="block text-sm font-semibold text-gray-900 mb-4">
                Wybierz usługi <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($this->formattedServices as $key => $service)
                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:scale-[1.02] @if(in_array($key, $serviceTypes)) border-emerald-500 bg-white text-gray-900 @else bg-white border-gray-200 hover:border-gray-300 text-gray-900 @endif"
                       wire:click.prevent="toggleServiceType('{{ $key }}')"
                       wire:key="main-service-{{ $key }}">
                    <input type="checkbox"
                           value="{{ $key }}"
                           class="sr-only"
                           @checked(in_array($key, $serviceTypes))>
                    <span class="text-2xl mr-3">{{ $service['icon'] }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm">{{ $service['title'] }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $service['desc'] }}</div>
                    </div>
                    @if(in_array($key, $serviceTypes))
                        <span class="text-emerald-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @endif
                </label>
            @endforeach
            </div>
            @error('serviceTypes')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <span class="mr-1">⚠️</span>
                    {{ $message }}
                </p>
            @enderror
            <p class="mt-4 text-xs text-gray-500 italic">
                💡 Wszystkie dostępne usługi są pobierane z bazy danych i zarządzane przez administratorów.
            </p>
        </div>
    </div>
</div>
