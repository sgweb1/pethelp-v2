/**
 * High-performance map component with caching and optimization
 */
class OptimizedMapComponent {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            center: [52.2297, 21.0122], // Warsaw
            zoom: 12,
            maxZoom: 18,
            minZoom: 6,
            clusterThreshold: 10, // Zoom level below which to use clustering
            debounceDelay: 300, // Debounce map movements
            cacheTimeout: 300000, // 5 minutes cache
            maxItems: 1000,
            ...options
        };

        this.map = null;
        this.markers = new Map();
        this.clusters = new Map();
        this.cache = new Map();
        this.lastRequest = null;
        this.debounceTimer = null;
        this.currentFilters = {};

        this.init();
    }

    init() {
        this.initMap();
        this.setupEventListeners();
        this.loadInitialData();
    }

    initMap() {
        // Initialize Leaflet map
        this.map = L.map(this.element, {
            center: this.options.center,
            zoom: this.options.zoom,
            maxZoom: this.options.maxZoom,
            minZoom: this.options.minZoom,
            zoomControl: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            touchZoom: true,
            keyboard: true,
            worldCopyJump: true
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: this.options.maxZoom
        }).addTo(this.map);

        // Setup map event handlers
        this.map.on('moveend', this.handleMapMove.bind(this));
        this.map.on('zoomend', this.handleZoomChange.bind(this));
    }

    setupEventListeners() {
        // Listen for filter changes from parent component
        document.addEventListener('map-filters-changed', (e) => {
            this.updateFilters(e.detail);
        });

        // Listen for cache invalidation
        document.addEventListener('map-cache-invalidate', () => {
            this.clearCache();
        });

        // Performance monitoring
        if (window.performance && window.performance.mark) {
            this.enablePerformanceTracking();
        }
    }

    enablePerformanceTracking() {
        this.performanceMetrics = {
            apiCalls: 0,
            cacheHits: 0,
            cacheMisses: 0,
            avgResponseTime: 0
        };

        // Track metrics every 30 seconds
        setInterval(() => {
            if (this.performanceMetrics.apiCalls > 0) {
                console.log('Map Performance:', this.performanceMetrics);
            }
        }, 30000);
    }

    updateFilters(filters) {
        this.currentFilters = { ...filters };
        this.debounceDataUpdate();
    }

    handleMapMove() {
        this.debounceDataUpdate();
    }

    handleZoomChange() {
        const zoom = this.map.getZoom();
        const usesClustering = zoom < this.options.clusterThreshold;

        if (usesClustering !== this.isClusterMode) {
            this.isClusterMode = usesClustering;
            this.clearMarkers();
        }

        this.debounceDataUpdate();
        this.dispatchEvent('zoom-changed', { zoom, clusterMode: this.isClusterMode });
    }

    debounceDataUpdate() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        this.debounceTimer = setTimeout(() => {
            this.updateMapData();
        }, this.options.debounceDelay);
    }

    async updateMapData() {
        const bounds = this.map.getBounds();
        const zoom = this.map.getZoom();

        const requestParams = {
            bounds: [
                bounds.getSouth(),
                bounds.getWest(),
                bounds.getNorth(),
                bounds.getEast()
            ],
            zoom_level: zoom,
            ...this.currentFilters
        };

        // Check cache first
        const cacheKey = this.generateCacheKey(requestParams);
        const cachedData = this.getFromCache(cacheKey);

        if (cachedData) {
            this.renderData(cachedData);
            this.trackMetric('cacheHits');
            return;
        }

        // Cancel previous request if still pending
        if (this.lastRequest && !this.lastRequest.signal.aborted) {
            this.lastRequest.abort();
        }

        // Create new request with abort controller
        const controller = new AbortController();
        this.lastRequest = controller;

        try {
            this.setLoading(true);
            const startTime = performance.now();

            let data;
            if (this.isClusterMode) {
                data = await this.fetchClusterData(requestParams, controller.signal);
            } else {
                data = await this.fetchMapItems(requestParams, controller.signal);
            }

            const endTime = performance.now();
            this.trackMetric('apiCalls');
            this.trackMetric('cacheMisses');
            this.updateAvgResponseTime(endTime - startTime);

            // Cache the result
            this.addToCache(cacheKey, data);

            // Render data
            this.renderData(data);

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Map data fetch error:', error);
                this.showError('Failed to load map data. Please try again.');
            }
        } finally {
            this.setLoading(false);
        }
    }

    async fetchMapItems(params, signal) {
        const url = new URL('/api/map/items', window.location.origin);
        Object.entries(params).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => url.searchParams.append(`${key}[]`, v));
            } else if (value !== null && value !== undefined) {
                url.searchParams.set(key, value);
            }
        });

        const response = await fetch(url, { signal });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'API request failed');
        }

        return {
            type: 'items',
            items: result.data.items,
            count: result.data.count
        };
    }

    async fetchClusterData(params, signal) {
        const url = new URL('/api/map/clusters', window.location.origin);
        url.searchParams.set('bounds', JSON.stringify(params.bounds));
        url.searchParams.set('zoom_level', params.zoom_level);

        const response = await fetch(url, { signal });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'API request failed');
        }

        return {
            type: 'clusters',
            ...result.data
        };
    }

    renderData(data) {
        this.clearMarkers();

        if (data.type === 'clusters') {
            this.renderClusters(data.clusters);
            this.renderMarkers(data.markers);
        } else {
            this.renderMarkers(data.items);
        }

        this.dispatchEvent('data-updated', {
            type: data.type,
            count: data.count || data.clusters?.length || data.markers?.length || 0
        });
    }

    renderMarkers(items) {
        if (!Array.isArray(items)) return;

        items.forEach(item => {
            const marker = this.createMarker(item);
            if (marker) {
                this.markers.set(item.id, marker);
                marker.addTo(this.map);
            }
        });
    }

    renderClusters(clusters) {
        if (!Array.isArray(clusters)) return;

        clusters.forEach((cluster, index) => {
            const clusterMarker = this.createClusterMarker(cluster, index);
            if (clusterMarker) {
                this.clusters.set(index, clusterMarker);
                clusterMarker.addTo(this.map);
            }
        });
    }

    createMarker(item) {
        if (!item.latitude || !item.longitude) return null;

        const icon = this.getIconForItem(item);
        const marker = L.marker([item.latitude, item.longitude], { icon })
            .bindPopup(this.createPopupContent(item));

        return marker;
    }

    createClusterMarker(cluster, index) {
        if (!cluster.lat || !cluster.lng) return null;

        const size = Math.min(Math.max(cluster.count / 10, 20), 60);
        const color = cluster.featured_count > 0 ? '#ef4444' :
                     cluster.urgent_count > 0 ? '#f97316' : '#3b82f6';

        const icon = L.divIcon({
            html: `<div style="
                background: ${color};
                color: white;
                width: ${size}px;
                height: ${size}px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: ${Math.max(10, size / 4)}px;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            ">${cluster.count}</div>`,
            className: 'cluster-marker',
            iconSize: [size, size],
            iconAnchor: [size / 2, size / 2]
        });

        const marker = L.marker([cluster.lat, cluster.lng], { icon })
            .bindPopup(`
                <div class="cluster-popup">
                    <h3>Obszar z ${cluster.count} elementami</h3>
                    ${cluster.featured_count > 0 ? `<p>🌟 ${cluster.featured_count} wyróżnionych</p>` : ''}
                    ${cluster.urgent_count > 0 ? `<p>⚡ ${cluster.urgent_count} pilnych</p>` : ''}
                    <p><small>Przybliż mapę, aby zobaczyć szczegóły</small></p>
                </div>
            `);

        return marker;
    }

    getIconForItem(item) {
        const color = item.is_featured ? '#ef4444' :
                     item.is_urgent ? '#f97316' : '#3b82f6';

        const iconHtml = `
            <div style="
                background: ${color};
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            ">
                ${this.getIconSymbol(item.content_type)}
            </div>
        `;

        return L.divIcon({
            html: iconHtml,
            className: 'custom-marker',
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15]
        });
    }

    getIconSymbol(contentType) {
        const icons = {
            service: '🔧',
            event: '📅',
            adoption: '❤️',
            lost_pet: '🔍',
            found_pet: '✅',
            supplies: '🛍️'
        };
        return icons[contentType] || '📍';
    }

    createPopupContent(item) {
        return `
            <div class="map-popup">
                <h3>${item.title}</h3>
                <p>${item.description_short || ''}</p>
                ${item.category_name ? `<p><strong>Kategoria:</strong> ${item.category_name}</p>` : ''}
                ${item.price_from ? `<p><strong>Cena:</strong> od ${item.price_from} ${item.currency}</p>` : ''}
                ${item.rating_avg ? `<p><strong>Ocena:</strong> ${item.rating_avg}/5 (${item.rating_count} ocen)</p>` : ''}
                <div class="popup-actions">
                    <button onclick="window.viewItem('${item.content_type}', ${item.id})">Zobacz szczegóły</button>
                </div>
            </div>
        `;
    }

    clearMarkers() {
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers.clear();

        this.clusters.forEach(cluster => this.map.removeLayer(cluster));
        this.clusters.clear();
    }

    generateCacheKey(params) {
        return btoa(JSON.stringify(params)).replace(/[/+=]/g, '');
    }

    getFromCache(key) {
        const entry = this.cache.get(key);
        if (!entry) return null;

        if (Date.now() - entry.timestamp > this.options.cacheTimeout) {
            this.cache.delete(key);
            return null;
        }

        return entry.data;
    }

    addToCache(key, data) {
        // Limit cache size
        if (this.cache.size > 50) {
            const oldestKey = this.cache.keys().next().value;
            this.cache.delete(oldestKey);
        }

        this.cache.set(key, {
            data,
            timestamp: Date.now()
        });
    }

    clearCache() {
        this.cache.clear();
        this.trackMetric('cacheClears');
    }

    trackMetric(metric) {
        if (this.performanceMetrics) {
            this.performanceMetrics[metric] = (this.performanceMetrics[metric] || 0) + 1;
        }
    }

    updateAvgResponseTime(responseTime) {
        if (this.performanceMetrics) {
            const currentAvg = this.performanceMetrics.avgResponseTime;
            const count = this.performanceMetrics.apiCalls;
            this.performanceMetrics.avgResponseTime = ((currentAvg * (count - 1)) + responseTime) / count;
        }
    }

    setLoading(loading) {
        this.dispatchEvent('loading-changed', { loading });
    }

    showError(message) {
        this.dispatchEvent('error', { message });
    }

    dispatchEvent(name, detail = {}) {
        const event = new CustomEvent(`map-${name}`, { detail });
        this.element.dispatchEvent(event);
    }

    // Public API methods
    setCenter(lat, lng, zoom = null) {
        this.map.setView([lat, lng], zoom || this.map.getZoom());
    }

    setZoom(zoom) {
        this.map.setZoom(zoom);
    }

    getBounds() {
        return this.map.getBounds();
    }

    fitBounds(bounds) {
        this.map.fitBounds(bounds);
    }

    loadInitialData() {
        this.updateMapData();
    }

    destroy() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        if (this.lastRequest) {
            this.lastRequest.abort();
        }

        this.clearMarkers();
        this.clearCache();

        if (this.map) {
            this.map.remove();
        }
    }
}

// Global function for popup actions
window.viewItem = function(contentType, id) {
    // This can be customized based on your routing
    const urls = {
        service: `/services/${id}`,
        event: `/events/${id}`,
        adoption: `/adoptions/${id}`,
        lost_pet: `/lost-pets/${id}`,
        found_pet: `/found-pets/${id}`,
        supplies: `/supplies/${id}`
    };

    const url = urls[contentType];
    if (url) {
        window.location.href = url;
    }
};

// Export for use in other scripts
window.OptimizedMapComponent = OptimizedMapComponent;