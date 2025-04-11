import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Function to get and send current location
window.sendCurrentLocation = function() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const long = position.coords.longitude;

            // First, set the location via API
            axios.post('/api/set-location', { lat, long })
                .then(response => {
                    console.log('Location set successfully:', response.data);
                    
                    // Trigger the Time In action in Filament
                    // This assumes you're using Filament's frontend
                    const timeInButton = document.querySelector('[data-action="Time In Today"]');
                    if (timeInButton) {
                        timeInButton.click();
                    } else {
                        console.warn('Time In button not found');
                    }
                })
                .catch(error => {
                    console.error('Error setting location:', error);
                });
        }, function(error) {
            console.error('Geolocation error:', error.message);
        });
    } else {
        console.error('Geolocation is not supported by this browser.');
    }
};

// Listen for location updates on the channel
window.Echo.channel('location')
    .listen('SendPosition', (event) => {
        console.log('Received location update:', event.location);
        // You can add additional logic here to handle the received location
    });
