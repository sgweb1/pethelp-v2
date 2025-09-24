export function createMapComponent() {
    return {
        map: null,
        vectorLayer: null,
        vectorSource: null,
        markerFeatures: [],
        highlightFeature: null, // Separate feature for highlighting
        ol: null,
        moveTimeout: null,
        updateMarkersTimeout: null,
        markerCache: new Map(),
        currentZoom: 7,
        lastBounds: null,

        // Performance settings
        MAX_MARKERS_VISIBLE: 200,

        async initMap() {
            try {
                await this.loadOpenLayers();

                // Sprawdź czy kontener mapy istnieje
                const mapContainer = document.getElementById('map-container');
                if (!mapContainer) {
                    console.error('Map container not found');
                    return;
                }

                // Sprawdź czy OpenLayers zostało załadowane
                if (!this.ol) {
                    throw new Error('OpenLayers nie załadował się poprawnie');
                }

                this.setupMap();
                this.setupEventListeners();
            } catch (error) {
                console.error('Error initializing map:', error);
                this.showMapError(`Błąd inicjalizacji mapy: ${error.message}`);
            }
        },

        async loadOpenLayers() {
            // Load OpenLayers CSS
            const cssPromise = new Promise((resolve, reject) => {
                let cssLink = document.querySelector('link[href*="ol.css"]');

                if (cssLink) {
                    resolve();
                    return;
                }

                cssLink = document.createElement('link');
                cssLink.rel = 'stylesheet';
                cssLink.href = 'https://cdn.jsdelivr.net/npm/ol@8.2.0/ol.css';

                cssLink.onload = () => resolve();
                cssLink.onerror = () => reject(new Error('Failed to load OpenLayers CSS'));

                document.head.appendChild(cssLink);
            });

            try {
                // Load CSS and wait for it
                await cssPromise;

                // Use script loading directly instead of ES modules
                await this.loadOpenLayersScript();
            } catch (error) {
                console.error('Failed to load OpenLayers:', error);
                await this.loadOpenLayersScript();
            }
        },

        async loadOpenLayersScript() {
            if (window.ol) {
                this.ol = window.ol;
                // Sprawdź kompletność modułów z window.ol
                if (this.ol && this.ol.source && this.ol.layer && this.ol.Map && this.ol.View && this.ol.proj) {
                    return;
                } else {
                    console.warn('Window.ol is incomplete, loading fresh copy');
                }
            }

            return new Promise((resolve, reject) => {
                // Sprawdź czy script już istnieje
                if (document.querySelector('script[src*="ol@8.2.0"]')) {
                    console.log('OpenLayers script already exists, waiting for load...');
                    const checkInterval = setInterval(() => {
                        if (window.ol && window.ol.Map) {
                            clearInterval(checkInterval);
                            this.ol = window.ol;
                            resolve();
                        }
                    }, 100);

                    // Timeout after 10 seconds
                    setTimeout(() => {
                        clearInterval(checkInterval);
                        reject(new Error('OpenLayers script load timeout'));
                    }, 10000);
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/ol@8.2.0/dist/ol.js';
                script.onload = () => {
                    if (window.ol && window.ol.Map) {
                        this.ol = window.ol;
                        console.log('OpenLayers loaded via fallback script');
                        resolve();
                    } else {
                        reject(new Error('OpenLayers script loaded but ol object is invalid'));
                    }
                };
                script.onerror = () => {
                    console.error('Failed to load OpenLayers via script tag');
                    reject(new Error('Failed to load OpenLayers script'));
                };
                document.head.appendChild(script);
            });
        },

        setupMap() {
            if (!this.ol || !this.ol.source || !this.ol.layer || !this.ol.Map || !this.ol.View || !this.ol.proj) {
                console.error('OpenLayers modules not properly loaded:', this.ol);
                this.showMapError('OpenLayers nie załadował się poprawnie. Sprawdź połączenie z internetem.');
                return;
            }

            try {
                // Create vector source for individual markers
                this.vectorSource = new this.ol.source.Vector();

                // Create cluster source with dynamic clustering based on zoom
                this.clusterSource = new this.ol.source.Cluster({
                    distance: this.CLUSTER_DISTANCE,
                    minDistance: 15,
                    source: this.vectorSource,
                    geometryFunction: (feature) => {
                        // Dynamic clustering based on zoom level
                        const zoom = this.map ? this.map.getView().getZoom() : 7;

                        // Don't cluster at high zoom levels
                        if (zoom >= this.MIN_ZOOM_FOR_INDIVIDUAL) {
                            return null; // No clustering
                        }

                        return feature.getGeometry();
                    }
                });

                // Individual markers layer (for high zoom)
                this.vectorLayer = new this.ol.layer.Vector({
                    source: this.vectorSource,
                    style: this.createMarkerStyle.bind(this)
                });

                // Cluster layer (for low zoom)
                this.clusterLayer = new this.ol.layer.Vector({
                    source: this.clusterSource,
                    style: this.createClusterStyle.bind(this)
                });

                this.map = new this.ol.Map({
                    target: 'map-container',
                    layers: [
                        new this.ol.layer.Tile({
                            source: new this.ol.source.OSM()
                        }),
                        this.clusterLayer,
                        this.vectorLayer
                    ],
                    view: new this.ol.View({
                        center: this.ol.proj.fromLonLat([19.0, 52.0]),
                        zoom: 7
                    })
                });

                console.log('Map initialized successfully');

                // Handle zoom for layer visibility only (bounds listener handles data updates)
                this.map.getView().on('change:resolution', () => {
                    const zoom = Math.round(this.map.getView().getZoom());
                    this.updateLayerVisibility(zoom);
                });

                // Throttle move events to prevent excessive API calls
                this.map.on('moveend', () => {
                    clearTimeout(this.moveTimeout);
                    this.moveTimeout = setTimeout(() => {
                        const extent = this.map.getView().calculateExtent();
                        const bounds = this.ol.proj.transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
                        const livewireBounds = [bounds[1], bounds[0], bounds[3], bounds[2]];

                        // 🚀 Use UnifiedSearchController API directly but only on significant moves
                        // Check if bounds changed significantly to avoid excessive calls
                        if (this.hasBoundsChangedSignificantly(livewireBounds)) {
                            this.fetchMapData({
                                bounds: livewireBounds,
                                format: 'map',
                                limit: 200
                            });
                            this.lastBounds = livewireBounds;
                        }

                        // Keep Livewire event for backward compatibility
                        Livewire.dispatch('map-bounds-changed', [livewireBounds]);
                    }, 1000); // Increased from 500ms to 1000ms
                });

                this.map.once('postrender', () => {
                    // Set initial layer visibility
                    const initialZoom = Math.round(this.map.getView().getZoom());
                    this.updateLayerVisibility(initialZoom);

                    // 🚀 Request initial data from UnifiedSearchController API
                    console.log('🗺️ Map ready, requesting initial data...');
                    setTimeout(() => {
                        const extent = this.map.getView().calculateExtent();
                        const bounds = this.ol.proj.transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
                        const livewireBounds = [bounds[1], bounds[0], bounds[3], bounds[2]];
                        console.log('📍 Sending initial bounds:', livewireBounds);

                        // Use unified API first
                        this.fetchMapData({
                            bounds: livewireBounds,
                            format: 'map',
                            limit: 200
                        });

                        // Keep Livewire event for backward compatibility
                        Livewire.dispatch('map-bounds-changed', [livewireBounds]);
                    }, 1000);
                });
            } catch (error) {
                console.error('Error setting up map:', error);
                this.showMapError('Błąd podczas tworzenia mapy: ' + error.message);
            }
        },

        setupEventListeners() {
            // Debug Livewire availability
            console.log('Livewire available:', typeof window.Livewire);
            console.log('Livewire object:', window.Livewire);

            // Listen for all Livewire events for debugging
            if (window.Livewire) {
                window.Livewire.hook('message.processed', (message, component) => {
                    console.log('Livewire message processed:', message, component);
                });
            } else {
                console.error('Livewire not available! Cannot listen for events.');
                // Try to wait for Livewire to load
                setTimeout(() => {
                    console.log('Checking Livewire again after delay:', typeof window.Livewire);
                    if (window.Livewire) {
                        this.setupLivewireListeners();
                    }
                }, 2000);
                return;
            }

            this.setupLivewireListeners();
            this.setupAlpineEventListeners();
        },

        setupLivewireListeners() {
            // Listen for location updates from Livewire
            Livewire.on('map-center-updated', (data) => {
                if (data && data.latitude && data.longitude) {
                    this.animateToLocation(data.latitude, data.longitude);
                }
            });

            // Listen for address selection
            Livewire.on('location-updated', (locationData) => {
                if (locationData && locationData.latitude && locationData.longitude) {
                    this.animateToLocation(locationData.latitude, locationData.longitude);
                }
            });
            console.log('Setting up Livewire listeners...');
            Livewire.on('map-data-updated', (data) => {
                try {
                    if (data && this.vectorLayer) {
                        // Check if data is array directly or nested
                        // Livewire wraps parameters in array, so check if first element is array
                        const markersData = Array.isArray(data) && Array.isArray(data[0]) ? data[0] : data;

                        // Debounce updateMarkers to prevent rapid updates during zoom/pan
                        clearTimeout(this.updateMarkersTimeout);
                        this.updateMarkersTimeout = setTimeout(() => {
                            this.updateMarkers(markersData);
                        }, 200);
                    } else {
                        console.error('❌ Invalid map data or vector layer not ready', {
                            hasData: !!data,
                            hasFirstElement: !!data?.[0],
                            hasVectorLayer: !!this.vectorLayer,
                            data: data
                        });
                    }
                } catch (error) {
                    console.error('❌ Error in Livewire event handler:', error);
                }
            });
        },

        setupAlpineEventListeners() {
            // Listen for Alpine.js events from search results for marker highlighting
            document.addEventListener('highlight-map-marker', (event) => {
                const markerId = event.detail?.id;
                this.highlightMarker(markerId);
            });

            document.addEventListener('focus-map-marker', (event) => {
                const markerId = event.detail?.id;
                this.focusMarker(markerId);
            });

            console.log('Alpine.js event listeners setup for map marker highlighting');
        },

        highlightMarker(markerId) {
            if (!this.vectorSource || !this.ol) {
                return;
            }

            // Remove previous highlight feature if it exists
            if (this.highlightFeature) {
                this.vectorSource.removeFeature(this.highlightFeature);
                this.highlightFeature = null;
            }

            // If markerId is provided, create highlight overlay
            if (markerId) {
                const targetFeature = this.markerFeatures.find(feature => {
                    const data = feature.get('data');
                    return data.id == markerId;
                });

                if (targetFeature) {
                    const geometry = targetFeature.getGeometry();
                    const data = targetFeature.get('data');

                    // Create a new feature for highlighting - don't modify the original
                    this.highlightFeature = new this.ol.Feature({
                        geometry: geometry.clone(), // Clone geometry to avoid reference issues
                        data: data
                    });

                    // Create highlighted style for the overlay
                    const highlightedStyle = this.createHighlightedMarkerStyle(this.highlightFeature);
                    this.highlightFeature.setStyle(highlightedStyle);

                    // Add highlight feature to map
                    this.vectorSource.addFeature(this.highlightFeature);

                    console.log(`🎯 Highlighted marker ${markerId} at ${data.lat}, ${data.lng}`);
                }
            }
        },

        focusMarker(markerId) {
            if (!this.map || !this.ol || !markerId) {
                return;
            }

            const targetFeature = this.markerFeatures.find(feature => {
                const data = feature.get('data');
                return data.id == markerId;
            });

            if (targetFeature) {
                const geometry = targetFeature.getGeometry();
                const coordinates = geometry.getCoordinates();

                // Animate to marker location
                this.map.getView().animate({
                    center: coordinates,
                    zoom: Math.max(this.map.getView().getZoom(), 14),
                    duration: 500
                });

                // Highlight the marker
                this.highlightMarker(markerId);
                console.log(`🎯 Focused on marker ${markerId}`);
            }
        },

        createHighlightedMarkerStyle(feature) {
            const data = feature.get('data');
            const color = data.color || data.category_color || '#3B82F6';
            const isFeatured = data.featured || data.is_featured || false;
            const isUrgent = data.urgent || data.is_urgent || false;

            console.log(`🎨 Creating highlighted style for marker ${data.id} at ${data.lat}, ${data.lng}`);

            // Important: Style should NEVER change geometry/position, only visual appearance
            return new this.ol.style.Style({
                image: new this.ol.style.Circle({
                    radius: isFeatured ? 12 : 10, // Larger radius for highlight
                    fill: new this.ol.style.Fill({
                        color: color
                    }),
                    stroke: new this.ol.style.Stroke({
                        color: '#FFFFFF',
                        width: 4 // Thicker stroke for highlight
                    })
                }),
                zIndex: 1000 // Higher z-index to show on top
            });
        },

        updateMarkers(data) {
            console.log('🗺️ updateMarkers called with data:', data);

            if (!this.vectorSource || !this.ol || !Array.isArray(data)) {
                console.warn('Cannot update markers: missing dependencies or invalid data', {
                    vectorSource: !!this.vectorSource,
                    ol: !!this.ol,
                    dataIsArray: Array.isArray(data),
                    data: data
                });
                return;
            }

            try {
                console.log(`🧹 Clearing ${this.markerFeatures.length} existing markers`);
                this.vectorSource.clear();
                this.markerFeatures = [];
                this.highlightFeature = null; // Clear highlight reference

                data.forEach((item, index) => {
                    // Handle both old format (coordinates object) and new UnifiedSearchController format (lng/lat directly)
                    let longitude, latitude;

                    if (item.lng !== undefined && item.lat !== undefined) {
                        // New UnifiedSearchController format
                        longitude = parseFloat(item.lng);
                        latitude = parseFloat(item.lat);
                    } else if (item.coordinates && item.coordinates.longitude && item.coordinates.latitude) {
                        // Old format with coordinates object
                        longitude = parseFloat(item.coordinates.longitude);
                        latitude = parseFloat(item.coordinates.latitude);
                    } else {
                        console.warn('Skipping invalid map item - missing coordinates:', {
                            item: item,
                            hasItem: !!item,
                            hasNewFormat: !!(item.lng && item.lat),
                            hasOldFormat: !!(item.coordinates?.longitude && item.coordinates?.latitude)
                        });
                        return;
                    }

                    // Sprawdź czy conversion zakończył się sukcesem
                    if (isNaN(longitude) || isNaN(latitude)) {
                        console.warn('Skipping item with invalid coordinate format:', {
                            item: item,
                            originalLng: item.coordinates?.longitude,
                            originalLat: item.coordinates?.latitude,
                            parsedLng: longitude,
                            parsedLat: latitude
                        });
                        return;
                    }

                    // Sprawdź czy coordinates są w prawidłowym zakresie
                    if (longitude < -180 || longitude > 180 || latitude < -90 || latitude > 90) {
                        console.warn('Skipping item with coordinates out of range:', item);
                        return;
                    }

                    const feature = new this.ol.Feature({
                        geometry: new this.ol.geom.Point(
                            this.ol.proj.fromLonLat([longitude, latitude])
                        ),
                        data: item
                    });

                    this.markerFeatures.push(feature);
                    this.vectorSource.addFeature(feature);

                    console.log(`📍 Added marker ${item.id} at ${latitude}, ${longitude} - ${item.title}`);
                });

                console.log(`✅ Successfully added ${this.markerFeatures.length} markers to map`);
            } catch (error) {
                console.error('Error in updateMarkers:', error);
            }
        },

        createMarkerStyle(feature) {
            const data = feature.get('data');

            // Handle both old and new data formats
            const color = data.color || data.category_color || '#3B82F6';
            const isFeatured = data.featured || data.is_featured || false;
            const isUrgent = data.urgent || data.is_urgent || false;

            return new this.ol.style.Style({
                image: new this.ol.style.Circle({
                    radius: isFeatured ? 8 : 6,
                    fill: new this.ol.style.Fill({
                        color: color
                    }),
                    stroke: new this.ol.style.Stroke({
                        color: isUrgent ? '#EF4444' : '#FFFFFF',
                        width: isUrgent ? 3 : 2
                    })
                })
            });
        },

        updateLayerVisibility(zoom) {
            // Show clusters at low zoom levels, individual markers at high zoom
            const showClusters = zoom < 10;

            if (this.clusterLayer) {
                this.clusterLayer.setVisible(showClusters);
            }

            if (this.vectorLayer) {
                this.vectorLayer.setVisible(!showClusters);
            }

            console.log(`Zoom ${zoom}: ${showClusters ? 'showing clusters' : 'showing individual markers'}`);
        },

        // Throttled version of updateMarkers for performance
        throttledUpdateMarkers: null,

        createClusterStyle(feature) {
            const features = feature.get('features');
            const size = features.length;

            if (size === 1) {
                // Single feature - use normal marker style
                return this.createMarkerStyle(features[0]);
            }

            // Cluster style with size indicator
            let radius = Math.min(Math.max(10 + size * 2, 15), 35);
            let fillColor = '#3B82F6';

            // Color based on cluster size
            if (size > 20) {
                fillColor = '#EF4444'; // Red for large clusters
            } else if (size > 10) {
                fillColor = '#F59E0B'; // Orange for medium clusters
            } else if (size > 5) {
                fillColor = '#10B981'; // Green for small clusters
            }

            return new this.ol.style.Style({
                image: new this.ol.style.Circle({
                    radius: radius,
                    fill: new this.ol.style.Fill({
                        color: fillColor
                    }),
                    stroke: new this.ol.style.Stroke({
                        color: '#FFFFFF',
                        width: 2
                    })
                }),
                text: new this.ol.style.Text({
                    text: size.toString(),
                    fill: new this.ol.style.Fill({
                        color: '#FFFFFF'
                    }),
                    font: 'bold 12px sans-serif'
                })
            });
        },

        /**
         * 🗺️ Animate map to specified location with smooth transition
         */
        animateToLocation(latitude, longitude, zoom = 12) {
            if (!this.map || !this.ol) {
                console.warn('⚠️ Map not initialized yet, queuing animation for later');

                // Retry after map initialization
                setTimeout(() => {
                    if (this.map && this.ol) {
                        this.animateToLocation(latitude, longitude, zoom);
                    }
                }, 2000);
                return;
            }

            const coordinates = this.ol.proj.fromLonLat([longitude, latitude]);
            const view = this.map.getView();

            console.log(`🎯 Animating map to: ${latitude}, ${longitude} (zoom: ${zoom})`);

            // Smooth animation to the location
            view.animate(
                {
                    center: coordinates,
                    zoom: zoom,
                    duration: 1000 // 1 second animation
                },
                () => {
                    console.log('✅ Map animation completed');
                    // Fetch new data for this location after animation
                    setTimeout(() => {
                        this.fetchMapData({
                            latitude: latitude,
                            longitude: longitude,
                            format: 'map',
                            limit: 200
                        });
                    }, 500);
                }
            );
        },

        /**
         * Check if bounds have changed significantly enough to warrant a new API call
         */
        hasBoundsChangedSignificantly(newBounds) {
            if (!this.lastBounds) {
                return true; // First time, always fetch
            }

            // Calculate difference threshold (about 10% of the current view)
            const threshold = 0.1;

            const [newSouth, newWest, newNorth, newEast] = newBounds;
            const [oldSouth, oldWest, oldNorth, oldEast] = this.lastBounds;

            const latDiff = Math.abs(newNorth - newSouth);
            const lngDiff = Math.abs(newEast - newWest);

            const latThreshold = latDiff * threshold;
            const lngThreshold = lngDiff * threshold;

            // Check if any boundary moved more than threshold
            return (
                Math.abs(newNorth - oldNorth) > latThreshold ||
                Math.abs(newSouth - oldSouth) > latThreshold ||
                Math.abs(newEast - oldEast) > lngThreshold ||
                Math.abs(newWest - oldWest) > lngThreshold
            );
        },

        /**
         * 🚀 Fetch map data from UnifiedSearchController API
         */
        async fetchMapData(params = {}) {
            try {
                const defaultParams = {
                    format: 'map',
                    limit: 200,
                    content_type: 'pet_sitter' // Default to pet_sitter
                };

                const searchParams = new URLSearchParams({
                    ...defaultParams,
                    ...params
                });

                const response = await fetch(`/api/search?${searchParams.toString()}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success && data.data && data.data.markers) {
                    console.log(`🚀 Fetched ${data.data.markers.length} markers from UnifiedSearchController`);
                    this.updateMarkers(data.data.markers);
                } else {
                    console.warn('No marker data received from API', data);
                }

            } catch (error) {
                console.error('Failed to fetch map data:', error);
                // Fallback to Livewire event if API fails
                const extent = this.map.getView().calculateExtent();
                const bounds = this.ol.proj.transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
                const livewireBounds = [bounds[1], bounds[0], bounds[3], bounds[2]];
                Livewire.dispatch('map-bounds-changed', [livewireBounds]);
            }
        },

        showMapError(message) {
            const mapContainer = document.getElementById('map-container');
            if (mapContainer) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'flex items-center justify-center h-full bg-gray-100';

                const centerDiv = document.createElement('div');
                centerDiv.className = 'text-center p-8';

                const iconDiv = document.createElement('div');
                iconDiv.className = 'text-red-500 mb-4';

                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('class', 'w-16 h-16 mx-auto');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('viewBox', '0 0 24 24');

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('d', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z');

                svg.appendChild(path);
                iconDiv.appendChild(svg);

                const title = document.createElement('h3');
                title.className = 'text-lg font-medium text-gray-900 mb-2';
                title.textContent = 'Błąd ładowania mapy';

                const description = document.createElement('p');
                description.className = 'text-gray-600 mb-4';
                description.textContent = message;

                const button = document.createElement('button');
                button.className = 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors';
                button.textContent = 'Odśwież stronę';
                button.onclick = () => window.location.reload();

                centerDiv.appendChild(iconDiv);
                centerDiv.appendChild(title);
                centerDiv.appendChild(description);
                centerDiv.appendChild(button);
                errorDiv.appendChild(centerDiv);

                mapContainer.innerHTML = '';
                mapContainer.appendChild(errorDiv);
            }
        }
    };
}