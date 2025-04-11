document.addEventListener('DOMContentLoaded', () => {
    // Function to get current location
    function getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    (error) => {
                        reject({
                            error: error.message
                        });
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                reject({
                    error: 'Geolocation is not supported by this browser.'
                });
            }
        });
    }

    // Listen for custom event from Filament
    document.addEventListener('filament-geolocation-request', async (event) => {
        try {
            const location = await getCurrentLocation();
            
            // Dispatch event with location data
            document.dispatchEvent(new CustomEvent('filament-geolocation-response', {
                detail: location
            }));
        } catch (error) {
            // Dispatch error event
            document.dispatchEvent(new CustomEvent('filament-geolocation-response', {
                detail: error
            }));
        }
    });
});
