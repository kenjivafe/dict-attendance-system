<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Tracking Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="p-8 max-w-md text-center bg-white rounded-lg shadow-md">
        <h1 class="mb-4 text-2xl font-bold">Location Tracking Test</h1>

        <div id="locationWarning" class="hidden p-4 mb-4 text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500">
            <p>Warning: Geolocation requires a secure (HTTPS) connection.</p>
            <p>For local development, use <code>https://localhost</code></p>
        </div>

        <button
            onclick="sendCurrentLocation()"
            class="px-4 py-2 text-white bg-blue-500 rounded transition hover:bg-blue-600"
        >
            Send My Current Location
        </button>

        <div id="locationDisplay" class="p-4 mt-4 bg-gray-100 rounded">
            <p>Location will be displayed here...</p>
        </div>
    </div>

    <script>
        window.sendCurrentLocation = function() {
            // Check if the page is served over HTTPS
            if (window.location.protocol !== 'https:') {
                document.getElementById('locationWarning').classList.remove('hidden');
                document.getElementById('locationDisplay').innerHTML =
                    'Geolocation requires a secure (HTTPS) connection.';
                return;
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const long = position.coords.longitude;

                    // Update display immediately
                    document.getElementById('locationDisplay').innerHTML =
                        `Latitude: ${lat}<br>Longitude: ${long}`;

                    // Send location to the API endpoint
                    axios.post('/api/position', { lat, long })
                        .then(response => {
                            console.log('Location sent successfully:', response.data);
                        })
                        .catch(error => {
                            console.error('Error sending location:', error);
                            document.getElementById('locationDisplay').innerHTML +=
                                `<br>API Error: ${error.message}`;
                        });
                }, function(error) {
                    console.error('Geolocation error:', error.message);
                    document.getElementById('locationDisplay').innerHTML =
                        `Error: ${error.message}`;
                });
            } else {
                console.error('Geolocation is not supported by this browser.');
                document.getElementById('locationDisplay').innerHTML =
                    'Geolocation is not supported by this browser.';
            }
        };

        // Listen for location updates on the channel
        window.Echo.channel('location')
            .listen('SendPosition', (event) => {
                console.log('Received location update:', event.location);
                document.getElementById('locationDisplay').innerHTML +=
                    `<br>Received: Lat ${event.location.lat}, Long ${event.location.long}`;
            });
    </script>
</body>
</html>
