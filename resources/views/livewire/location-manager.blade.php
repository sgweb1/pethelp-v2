<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Zarządzaj lokalizacjami</h2>
        <button
            wire:click="addLocation"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
        >
            + Dodaj lokalizację
        </button>
    </div>

    <!-- Locations List -->
    @if($locations && count($locations) > 0)
        <div class="grid gap-4">
            @foreach($locations as $location)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-medium text-gray-900">{{ $location->name }}</h3>
                                @if($location->is_primary)
                                    <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">
                                        Główna
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm">
                                {{ $location->street }}<br>
                                {{ $location->postal_code }} {{ $location->city }}<br>
                                {{ $location->country }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$location->is_primary)
                                <button
                                    wire:click="setPrimary({{ $location->id }})"
                                    class="text-sm text-indigo-600 hover:text-indigo-800"
                                    title="Ustaw jako główną"
                                >
                                    Ustaw główną
                                </button>
                            @endif
                            <button
                                wire:click="editLocation({{ $location->id }})"
                                class="text-sm text-gray-600 hover:text-gray-800"
                            >
                                Edytuj
                            </button>
                            <button
                                wire:click="deleteLocation({{ $location->id }})"
                                class="text-sm text-red-600 hover:text-red-800"
                                onclick="return confirm('Czy na pewno chcesz usunąć tę lokalizację?')"
                            >
                                Usuń
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <p>Nie masz jeszcze żadnych lokalizacji.</p>
            <p class="text-sm">Dodaj pierwszą lokalizację, aby rozpocząć.</p>
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-medium mb-4">
                    {{ $editingLocation ? 'Edytuj lokalizację' : 'Dodaj nową lokalizację' }}
                </h3>

                <form wire:submit.prevent="saveLocation" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa</label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="np. Dom, Praca"
                        >
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ulica i numer</label>
                        <input
                            type="text"
                            wire:model="street"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="ul. Przykładowa 123"
                        >
                        @error('street') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                            <input
                                type="text"
                                wire:model="city"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Warszawa"
                            >
                            @error('city') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy</label>
                            <input
                                type="text"
                                wire:model="postal_code"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="00-001"
                            >
                            @error('postal_code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kraj</label>
                        <input
                            type="text"
                            wire:model="country"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                        @error('country') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="is_primary"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <label class="ml-2 text-sm text-gray-700">Ustaw jako główną lokalizację</label>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button
                            wire:click="detectCurrentLocation"
                            type="button"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            📍 Wykryj moją lokalizację
                        </button>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Anuluj
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                        >
                            {{ $editingLocation ? 'Zapisz zmiany' : 'Dodaj lokalizację' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('detect-current-location', () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // Use reverse geocoding to get address
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10`)
                                .then(response => response.json())
                                .then(data => {
                                    const address = data.display_name || 'Twoja lokalizacja';
                                    @this.call('setDetectedLocation', lat, lng, address);
                                })
                                .catch(() => {
                                    @this.call('setDetectedLocation', lat, lng, 'Twoja lokalizacja');
                                });
                        },
                        function(error) {
                            alert('Nie udało się wykryć lokalizacji. Sprawdź uprawnienia przeglądarki.');
                        }
                    );
                } else {
                    alert('Geolokalizacja nie jest obsługiwana przez Twoją przeglądarkę.');
                }
            });
        });
    </script>
    @endpush
</div>