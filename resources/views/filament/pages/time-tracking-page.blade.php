<x-filament-panels::page>
    <div class="container p-6 mx-auto">
        <div class="flex gap-4 justify-between">
            <div class="mb-6 text-center">
                <div
                    id="liveClock"
                    class="text-3xl font-bold text-gray-800 dark:text-white"
                >
                    00:00:00
                </div>
                <div
                    id="liveDate"
                    class="text-lg text-left text-gray-600 dark:text-gray-300"
                >
                    Loading...
                </div>
            </div>
            <div class="text-center">
                <button
                    x-data
                    x-on:click="$wire.recordTimeEntry()"
                    class="px-4 py-2 font-bold text-black bg-blue-500 rounded dark:text-white hover:bg-blue-700"
                >
                    Record Time Entry
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-6 mb-6 md:grid-cols-2">
            <div class="p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="mb-2 text-lg font-bold">Time In (AM)</h2>
                <span>{{ $this->getTodayTimeEntry('time_in_am') }}</span>
            </div>

            <div class="p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="mb-2 text-lg font-bold">Time Out (AM)</h2>
                <span>{{ $this->getTodayTimeEntry('time_out_am') }}</span>
            </div>

            <div class="p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="mb-2 text-lg font-bold">Time In (PM)</h2>
                <span>{{ $this->getTodayTimeEntry('time_in_pm') }}</span>
            </div>

            <div class="p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800">
                <h2 class="mb-2 text-lg font-bold">Time Out (PM)</h2>
                <span>{{ $this->getTodayTimeEntry('time_out_pm') }}</span>
            </div>
        </div>


        <div class="text-center">
            <button
                id="getLocationBtn"
                class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-700"
            >
                Get Current Location
            </button>
        </div>
    </div>

    <script>
        function updateLiveClock() {
            const now = new Date();
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            const timeString = now.toLocaleTimeString([], timeOptions);
            const dateString = now.toLocaleDateString([], dateOptions);

            document.getElementById('liveClock').textContent = timeString;
            document.getElementById('liveDate').textContent = dateString;
        }

        // Update clock immediately and then every second
        updateLiveClock();
        setInterval(updateLiveClock, 1000);

        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Use Livewire to save location
                    @this.call('saveLocation', latitude, longitude);
                }, function(error) {
                    console.error('Error getting location:', error.message);
                    alert('Unable to retrieve your location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by your browser');
            }
        });
    </script>
</x-filament-panels::page>
