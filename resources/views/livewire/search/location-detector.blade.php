<div class="inline-block">
    {{-- Location Status Indicator --}}
    @if($location_detected)
        <div class="flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-green-800">Lokalizacja wykryta</p>
                @if($location_address)
                    <p class="text-xs text-green-600 truncate">{{ $location_address }}</p>
                @else
                    <p class="text-xs text-green-600">{{ $latitude }}, {{ $longitude }}</p>
                @endif
            </div>
            <button wire:click="clearLocation"
                    class="text-green-600 hover:text-green-800 transition-colors duration-200"
                    title="Wyczyść lokalizację">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @elseif($detecting)
        <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
            <svg class="w-4 h-4 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-blue-800">Wykrywanie lokalizacji...</span>
        </div>
    @else
        <button wire:click="startDetection"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Wykryj lokalizację
        </button>
    @endif
</div>

{{-- Geolocation JavaScript --}}
<script>
document.addEventListener('livewire:init', () => {
    // Listen for start geolocation event
    Livewire.on('start-geolocation', () => {
        detectUserLocation();
    });

    // Listen for detect-location event from other components
    Livewire.on('detect-location', () => {
        @this.call('startDetection');
    });

    function detectUserLocation() {
        if (!navigator.geolocation) {
            @this.call('failedDetection', 'Geolokalizacja nie jest obsługiwana przez tę przeglądarkę');
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000 // 5 minutes
        };

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Try to get address from coordinates using reverse geocoding
                reverseGeocode(latitude, longitude)
                    .then(address => {
                        @this.call('setLocation', latitude, longitude, address);
                    })
                    .catch(() => {
                        @this.call('setLocation', latitude, longitude, '');
                    });
            },
            (error) => {
                let errorMessage = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Dostęp do lokalizacji został zablokowany';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Informacje o lokalizacji są niedostępne';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Przekroczono czas oczekiwania na lokalizację';
                        break;
                    default:
                        errorMessage = 'Wystąpił nieznany błąd podczas wykrywania lokalizacji';
                        break;
                }
                @this.call('failedDetection', errorMessage);
            },
            options
        );
    }

    async function reverseGeocode(latitude, longitude) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&addressdetails=1&accept-language=pl`
            );

            if (!response.ok) {
                throw new Error('Geocoding failed');
            }

            const data = await response.json();

            if (data && data.display_name) {
                // Extract meaningful parts of the address
                const address = data.address;
                let formattedAddress = '';

                if (address.road) {
                    formattedAddress += address.road;
                    if (address.house_number) {
                        formattedAddress += ' ' + address.house_number;
                    }
                    formattedAddress += ', ';
                }

                if (address.city || address.town || address.village) {
                    formattedAddress += (address.city || address.town || address.village);
                } else if (address.suburb || address.neighbourhood) {
                    formattedAddress += (address.suburb || address.neighbourhood);
                }

                return formattedAddress || data.display_name;
            }

            throw new Error('No address found');
        } catch (error) {
            console.warn('Reverse geocoding failed:', error);
            throw error;
        }
    }
});
</script>
