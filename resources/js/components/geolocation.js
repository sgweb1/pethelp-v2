// Geolocation Component for Alpine.js
export default () => ({
    loading: false,
    error: null,
    position: null,

    async getCurrentLocation() {
        this.loading = true;
        this.error = null;

        if (!navigator.geolocation) {
            this.error = 'Geolocation is not supported by this browser.';
            this.loading = false;
            return;
        }

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 600000 // 10 minutes
                });
            });

            this.position = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy
            };

            // Dispatch event with location data
            this.$dispatch('location-detected', this.position);

        } catch (error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    this.error = 'Location access denied by user.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    this.error = 'Location information unavailable.';
                    break;
                case error.TIMEOUT:
                    this.error = 'Location request timed out.';
                    break;
                default:
                    this.error = 'An unknown error occurred.';
                    break;
            }
        } finally {
            this.loading = false;
        }
    },

    async reverseGeocode(lat, lng) {
        try {
            // Using OpenStreetMap Nominatim for reverse geocoding
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
            );

            if (!response.ok) {
                throw new Error('Geocoding failed');
            }

            const data = await response.json();
            return {
                address: data.display_name,
                city: data.address?.city || data.address?.town || data.address?.village,
                postcode: data.address?.postcode,
                country: data.address?.country
            };
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            return null;
        }
    }
});