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
                @php
                    $nextTimeEntryLabel = $this->getNextTimeEntryLabel();
                    $isAllRecorded = $nextTimeEntryLabel === 'All entries recorded';
                @endphp
                <button
                    x-data
                    x-on:click="$wire.recordTimeEntry()"
                    class="px-4 py-2 font-bold text-black rounded-lg {{ $isAllRecorded ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-primary-500 dark:text-white hover:bg-primary-700' }}"
                    {{ $isAllRecorded ? 'disabled' : '' }}
                >
                    {{ $nextTimeEntryLabel }}
                </button>



                <div class="text-center">
                    <button
                        id="getLocationBtn"
                        class="relative px-4 py-2 text-sm text-gray-700 bg-green-500 rounded dark:text-gray-500 hover:bg-green-700"
                    >
                        <span id="locationBtnText">Check Location</span>
                        <div id="locationLoadingSpinner" class="flex hidden absolute inset-0 justify-center items-center">
                            <svg class="mr-2 w-5 h-5 animate-spin text-gray-5 00" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="mx-1 text-gray-500">Checking</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-6 mb-6 md:grid-cols-2">
            <div class="p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time In (AM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_in_am') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_in_am') }}
                </div>
            </div>

            <div class="p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time Out (AM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_out_am') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_out_am') }}
                </div>
            </div>

            <div class="p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time In (PM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_in_pm') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_in_pm') }}
                </div>
            </div>

            <div class="p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time Out (PM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_out_pm') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_out_pm') }}
                </div>
            </div>
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
            // Show loading spinner
            const locationBtnText = document.getElementById('locationBtnText');
            const locationLoadingSpinner = document.getElementById('locationLoadingSpinner');

            // Disable button and show spinner
            this.disabled = true;
            locationBtnText.classList.add('invisible');
            locationLoadingSpinner.classList.remove('hidden');

            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Use Livewire to save location
                    @this.call('saveLocation', latitude, longitude).then(() => {
                        // Hide loading spinner
                        locationBtnText.classList.remove('invisible');
                        locationLoadingSpinner.classList.add('hidden');
                        document.getElementById('getLocationBtn').disabled = false;
                    });
                }, function(error) {
                    console.error('Error getting location:', error.message);
                    alert('Unable to retrieve your location: ' + error.message);

                    // Hide loading spinner
                    locationBtnText.classList.remove('invisible');
                    locationLoadingSpinner.classList.add('hidden');
                    document.getElementById('getLocationBtn').disabled = false;
                });
            } else {
                alert('Geolocation is not supported by your browser');

                // Hide loading spinner
                locationBtnText.classList.remove('invisible');
                locationLoadingSpinner.classList.add('hidden');
                document.getElementById('getLocationBtn').disabled = false;
            }
        });
    </script>
</x-filament-panels::page>
