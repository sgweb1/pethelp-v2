<div class="w-full h-full flex flex-col">

    {{-- Map Container - Always Visible, Full Height --}}
    <div class="flex-1 relative min-h-0 h-full">
        {{-- Map will be rendered here by JavaScript --}}
        <div id="search-map"
             wire:ignore
             class="w-full h-full"
             data-latitude="{{ $latitude ?? 52.2297 }}"
             data-longitude="{{ $longitude ?? 21.0122 }}"
             data-radius="{{ $radius }}"
             data-zoom="{{ $zoom_level }}"
             data-cluster-mode="{{ $cluster_mode ? 'true' : 'false' }}">
        </div>

        {{-- Map Loading Overlay --}}
        <div wire:loading.delay
             class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
            <div class="flex items-center gap-3 bg-white rounded-lg shadow-lg p-3">
                <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 text-sm">Ładowanie mapy...</span>
            </div>
        </div>

        {{-- Map Controls --}}
        <div class="absolute top-2 right-2 flex flex-col gap-1">
            {{-- Zoom Controls --}}
            <div class="leaflet-control-zoom leaflet-bar leaflet-control bg-white rounded shadow-lg overflow-hidden">
                <button class="block w-8 h-8 flex items-center justify-center hover:bg-gray-50 border-b"
                        onclick="zoomIn()"
                        title="Powiększ">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
                <button class="block w-8 h-8 flex items-center justify-center hover:bg-gray-50"
                        onclick="zoomOut()"
                        title="Pomniejsz">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                    </svg>
                </button>
            </div>

            {{-- Location Detection --}}
            <button class="leaflet-control-zoom leaflet-bar leaflet-control w-8 h-8 bg-white rounded shadow-lg flex items-center justify-center hover:bg-gray-50"
                    onclick="detectUserLocation()"
                    title="Wykryj moją lokalizację">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 1 1 -6 0 3 3 0 0 1 6 0z"/>
                </svg>
            </button>

            {{-- Center on Current Location --}}
            <button class="leaflet-control-zoom leaflet-bar leaflet-control w-8 h-8 bg-white rounded shadow-lg flex items-center justify-center hover:bg-gray-50"
                    onclick="centerOnLocation()"
                    title="Wycentruj na aktualnej lokalizacji">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Map JavaScript --}}
<script>
// Include Leaflet CSS and JS
if (!document.querySelector('link[href*="leaflet"]')) {
    const leafletCSS = document.createElement('link');
    leafletCSS.rel = 'stylesheet';
    leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(leafletCSS);
}

if (!window.L) {
    const leafletJS = document.createElement('script');
    leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    document.head.appendChild(leafletJS);
}

// Include MarkerCluster plugin
if (!document.querySelector('link[href*="MarkerCluster"]')) {
    const clusterCSS = document.createElement('link');
    clusterCSS.rel = 'stylesheet';
    clusterCSS.href = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css';
    document.head.appendChild(clusterCSS);

    const clusterDefaultCSS = document.createElement('link');
    clusterDefaultCSS.rel = 'stylesheet';
    clusterDefaultCSS.href = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css';
    document.head.appendChild(clusterDefaultCSS);
}

if (!window.L || !window.L.markerClusterGroup) {
    const clusterJS = document.createElement('script');
    clusterJS.src = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js';
    document.head.appendChild(clusterJS);
}

document.addEventListener('livewire:init', () => {
    let map;
    let markers = [];
    let markerClusterGroup;

    function initializeMap() {
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            return;
        }

        const mapElement = document.getElementById('search-map');
        if (!mapElement || map) return;

        // Get coordinates from URL parameters or dataset
        const urlParams = new URLSearchParams(window.location.search);
        const lat = parseFloat(urlParams.get('lat')) || parseFloat(mapElement.dataset.latitude) || 52.2297;
        const lng = parseFloat(urlParams.get('lng')) || parseFloat(mapElement.dataset.longitude) || 21.0122;
        const zoom = parseInt(urlParams.get('zoom')) || parseInt(mapElement.dataset.zoom) || 13;

        map = L.map('search-map').setView([lat, lng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add user location marker
        if (mapElement.dataset.latitude && mapElement.dataset.longitude) {
            const userIcon = L.divIcon({
                className: 'user-location-icon',
                html: '<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            L.marker([lat, lng], { icon: userIcon })
                .addTo(map)
                .bindPopup('Twoja lokalizacja')
                .openPopup();

            // Add radius circle if radius is set
            const radius = parseFloat(mapElement.dataset.radius) || 10;
            L.circle([lat, lng], {
                radius: radius * 1000, // Convert km to meters
                fillColor: '#3b82f6',
                color: '#3b82f6',
                weight: 1,
                opacity: 0.3,
                fillOpacity: 0.1
            }).addTo(map);
        }

        loadMapItems();

        // Listen for map events
        map.on('moveend zoomend', function() {
            const bounds = map.getBounds();
            const zoom = map.getZoom();
            const center = map.getCenter();

            // Update bounds for area-based search
            @this.call('updateMapBounds', [
                bounds.getSouth(),
                bounds.getWest(),
                bounds.getNorth(),
                bounds.getEast()
            ]);

            // Update zoom level
            @this.call('updateZoomLevel', zoom);

            // Update center coordinates
            @this.call('updateMapCenter', center.lat, center.lng);
        });

        // Listen for dragend to update center position
        map.on('dragend', function() {
            const center = map.getCenter();
            @this.call('updateMapCenter', center.lat, center.lng);
        });
    }

    function loadMapItems() {
        // Use the direct map items property
        const items = @json($this->directMapItems ?? []);
        console.log('Loading map items:', items.length);

        // Clear existing markers
        if (markerClusterGroup) {
            map.removeLayer(markerClusterGroup);
        }
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        // Create cluster group if clustering is enabled
        const clusterMode = document.getElementById('search-map').dataset.clusterMode === 'true';
        if (clusterMode && window.L && window.L.markerClusterGroup) {
            markerClusterGroup = L.markerClusterGroup({
                chunkedLoading: true,
                chunkInterval: 200,
                chunkDelay: 50
            });
        }

        // Add item markers
        items.forEach(item => {
            if (item.latitude && item.longitude) {
                const iconColor = getContentTypeColor(item.content_type);
                const isSpecial = item.is_featured || item.is_urgent;

                // Create custom icon
                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="w-6 h-6 ${iconColor} rounded-full flex items-center justify-center text-white text-xs font-bold ${isSpecial ? 'ring-2 ring-yellow-400' : ''}">
                        ${item.is_featured ? '★' : (item.is_urgent ? '!' : '•')}
                    </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                const marker = L.marker([
                    item.latitude,
                    item.longitude
                ], { icon: icon });

                // Store item ID for highlighting
                marker.itemId = item.id;

                const popupContent = createPopupContent(item);
                marker.bindPopup(popupContent);

                if (clusterMode && markerClusterGroup) {
                    markerClusterGroup.addLayer(marker);
                } else {
                    marker.addTo(map);
                }

                markers.push(marker);
            }
        });

        if (clusterMode && markerClusterGroup) {
            map.addLayer(markerClusterGroup);
        }
    }

    function createPopupContent(item) {
        return `
            <div class="p-3 min-w-64 max-w-80">
                <div class="flex items-start gap-3">
                    ${item.primary_image_url ?
                        `<img src="${item.primary_image_url}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0" alt="${item.title}">` :
                        '<div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center"><span class="text-gray-400 text-xs">Brak zdjęcia</span></div>'
                    }
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-sm text-gray-900 truncate">${item.title}</h4>
                        <p class="text-xs text-gray-600 mt-1">${item.category_name}</p>
                        ${item.description_short ? `<p class="text-xs text-gray-500 mt-1 line-clamp-2">${item.description_short}</p>` : ''}

                        <div class="flex items-center gap-2 mt-2">
                            ${item.price_from ? `<span class="text-xs font-medium text-green-600">${item.price_from} ${item.currency || 'zł'}</span>` : ''}
                            ${item.is_featured ? '<span class="text-xs bg-yellow-100 text-yellow-800 px-1 rounded">★ Wyróżnione</span>' : ''}
                            ${item.is_urgent ? '<span class="text-xs bg-red-100 text-red-800 px-1 rounded">! Pilne</span>' : ''}
                        </div>

                        ${item.rating_avg && item.rating_avg > 0 ?
                            `<div class="flex items-center gap-1 mt-1">
                                <span class="text-xs text-yellow-500">★</span>
                                <span class="text-xs text-gray-600">${item.rating_avg} (${item.rating_count || 0})</span>
                            </div>` : ''
                        }

                        <div class="flex gap-2 mt-2">
                            <a href="#" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">Zobacz</a>
                            <a href="#" class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Kontakt</a>
                        </div>

                        ${item.user ? `<p class="text-xs text-gray-500 mt-1">Przez: ${item.user.name}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-1">${item.city}</p>
                    </div>
                </div>
            </div>
        `;
    }

    function getContentTypeColor(contentType) {
        const colors = {
            'pet_sitter': 'bg-purple-500',
            'service': 'bg-blue-500',
            'event_public': 'bg-green-500',
            'advertisement': 'bg-orange-500'
        };
        return colors[contentType] || 'bg-gray-500';
    }

    // Initialize map when component loads
    Livewire.hook('message.processed', (message, component) => {
        if (component.fingerprint.name === 'search.search-map') {
            setTimeout(initializeMap, 100);
            // Reload map items when filters change
            if (map) {
                setTimeout(loadMapItems, 200);
            }
        }
    });

    // Listen for location detection events
    Livewire.on('detect-location', () => {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    @this.call('setLocation', lat, lng, 'Lokalizacja wykryta automatycznie');

                    if (map) {
                        map.setView([lat, lng], 13);
                    }
                },
                function(error) {
                    console.error('Error detecting location:', error);
                    alert('Nie udało się wykryć lokalizacji. Sprawdź ustawienia przeglądarki.');
                }
            );
        } else {
            alert('Geolokalizacja nie jest obsługiwana przez tę przeglądarkę.');
        }
    });

    // Listen for map center updates
    Livewire.on('map-center-updated', (data) => {
        if (map) {
            map.setView([data.latitude, data.longitude], 13);
        }
    });

    // Listen for marker highlighting from search results
    document.addEventListener('highlight-map-marker', (event) => {
        const markerId = event.detail?.id;
        highlightMapMarker(markerId);
    });

    // Listen for marker focusing from search results
    document.addEventListener('focus-map-marker', (event) => {
        const markerId = event.detail?.id;
        focusMapMarker(markerId);
    });

    // Listen for Livewire filter updates
    Livewire.on('filters-updated', () => {
        console.log('Filters updated, reloading map items');
        if (map) {
            setTimeout(loadMapItems, 100);
        }
    });

    Livewire.on('update-map-filters', () => {
        console.log('Map filters updated, reloading map items');
        if (map) {
            setTimeout(loadMapItems, 100);
        }
    });

    // Listen for URL updates
    Livewire.on('update-browser-url', (newUrl) => {
        if (history.replaceState) {
            history.replaceState(null, null, newUrl);
        }
    });

    function highlightMapMarker(markerId) {
        // Find marker by item ID and highlight it
        markers.forEach(marker => {
            const markerElement = marker.getElement ? marker.getElement() : marker._icon;
            if (markerElement) {
                if (marker.itemId === markerId) {
                    markerElement.style.transform = 'scale(1.3)';
                    markerElement.style.zIndex = '1000';
                    markerElement.style.filter = 'drop-shadow(0 0 10px rgba(59, 130, 246, 0.8))';
                } else {
                    markerElement.style.transform = 'scale(1)';
                    markerElement.style.zIndex = 'auto';
                    markerElement.style.filter = 'none';
                }
            }
        });
    }

    function focusMapMarker(markerId) {
        // Find marker by item ID and center map on it
        const targetMarker = markers.find(marker => marker.itemId === markerId);
        if (targetMarker && map) {
            const latlng = targetMarker.getLatLng();
            map.setView(latlng, Math.max(14, map.getZoom()));

            // Open popup
            setTimeout(() => {
                targetMarker.openPopup();
            }, 300);
        }
    }

    // Global functions for map controls
    window.zoomIn = function() {
        if (map) map.zoomIn();
    };

    window.zoomOut = function() {
        if (map) map.zoomOut();
    };

    window.detectUserLocation = function() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    @this.call('setLocation', lat, lng, 'Lokalizacja wykryta automatycznie');

                    if (map) {
                        map.setView([lat, lng], 13);
                    }
                },
                function(error) {
                    console.error('Error detecting location:', error);
                    alert('Nie udało się wykryć lokalizacji. Sprawdź ustawienia przeglądarki.');
                }
            );
        } else {
            alert('Geolokalizacja nie jest obsługiwana przez tę przeglądarkę.');
        }
    };

    window.centerOnLocation = function() {
        const mapElement = document.getElementById('search-map');
        const lat = parseFloat(mapElement.dataset.latitude);
        const lng = parseFloat(mapElement.dataset.longitude);

        if (map && lat && lng) {
            map.setView([lat, lng], 14);
        }
    };

    // Initialize on page load
    setTimeout(initializeMap, 500);
});
</script>