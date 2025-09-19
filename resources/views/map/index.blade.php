<x-layouts.app title="Mapa PetHelp - Zobacz wszystkie ogłoszenia, wydarzenia i usługi">
<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">🗺️ Mapa PetHelp</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Odkryj adopcje, sprzedaż, wydarzenia, usługi i zagubione zwierzęta w Twojej okolicy
                        </p>
                    </div>

                    <div class="flex items-center space-x-3">
                        {{-- Quick Actions --}}
                        <a
                            href="{{ route('events.create') }}"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            <x-icon name="plus" class="w-4 h-4 mr-1.5" />
                            Dodaj wydarzenie
                        </a>

                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            <x-icon name="briefcase" class="w-4 h-4 mr-1.5" />
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Component --}}
    <livewire:map-view />

    {{-- Usage Info Modal (Hidden by default) --}}
    <div x-data="{ showInfo: false }" x-cloak>
        {{-- Info Button --}}
        <button
            @click="showInfo = true"
            class="fixed bottom-4 right-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 shadow-lg transition-colors z-30"
            title="Informacje o mapie"
        >
            <x-icon name="info" class="w-5 h-5" />
        </button>

        {{-- Modal --}}
        <div x-show="showInfo" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
            <div class="bg-white rounded-lg p-6 max-w-md mx-4 max-h-96 overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Jak używać mapy</h3>
                    <button @click="showInfo = false" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4 text-sm text-gray-600">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-1">🔍 Wyszukiwanie</h4>
                        <p>Użyj pola wyszukiwania, aby znaleźć konkretne ogłoszenia, wydarzenia lub usługi.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-1">🏷️ Filtry kategorii</h4>
                        <p>Wybierz kategorie, które Cię interesują: adopcja, sprzedaż, wydarzenia, usługi, etc.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-1">⭐ Oznaczenia</h4>
                        <p>Większe punkty to wyróżnione ogłoszenia, czerwona ramka oznacza pilne.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-1">💰 Filtry cenowe</h4>
                        <p>Użyj pól "Od" i "Do", aby filtrować według ceny.</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-1">📱 Responsywność</h4>
                        <p>Mapa dostosowuje się do wielkości ekranu i działa na urządzeniach mobilnych.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="showInfo = false"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Zamknij
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>