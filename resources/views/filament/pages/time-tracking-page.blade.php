<x-filament-panels::page>
    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="flex hidden fixed inset-0 z-50 justify-center items-center">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative p-6 mx-auto w-80 bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <h3 id="modalTitle" class="mb-4 text-lg font-bold text-gray-900 dark:text-white"></h3>
            <p id="modalMessage" class="mb-6 text-gray-500 dark:text-gray-300"></p>
            <br>
            <div class="flex gap-2 justify-end">
                <button id="modalCancelBtn" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
                    Cancel
                </button>
                <button id="modalConfirmBtn" class="px-4 py-2 text-sm text-white rounded bg-primary-600 hover:bg-primary-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    <div class="container p-6 mx-auto">
        <div class="flex gap-4 justify-between">
            <div class="mb-6 text-center">
                <div
                    id="liveClock"
                    class="text-xl font-bold text-left text-gray-800 md:text-3xl dark:text-white"
                >
                    00:00:00
                </div>
                <div
                    id="liveDate"
                    class="text-left text-gray-600 text-md md:text-lg dark:text-gray-300"
                >
                    Loading...
                </div>
            </div>
            <div class="text-center">




                <div class="text-center">
                    <button
                        id="getLocationBtn"
                        class="relative px-4 py-2 text-xs text-gray-700 bg-green-500 rounded md:text-sm dark:text-gray-500 hover:bg-green-700"
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
            <button
                id="timeInAmBtn"
                @class([
                    'w-full p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700',
                    'cursor-pointer' => $this->getTodayTimeEntry('time_in_am') === 'Not recorded',
                    'cursor-not-allowed opacity-75' => $this->getTodayTimeEntry('time_in_am') !== 'Not recorded',
                ])
                {{ $this->getTodayTimeEntry('time_in_am') !== 'Not recorded' ? 'disabled' : '' }}
            >
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time In (AM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_in_am') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_in_am') }}
                </div>
            </button>

            <button
                id="timeOutAmBtn"
                @class([
                    'w-full p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700',
                    'cursor-pointer' => $this->getTodayTimeEntry('time_out_am') === 'Not recorded' && $this->getTodayTimeEntry('time_in_am') !== 'Not recorded',
                    'cursor-not-allowed opacity-75' => $this->getTodayTimeEntry('time_out_am') !== 'Not recorded' || $this->getTodayTimeEntry('time_in_am') === 'Not recorded',
                ])
                {{ $this->getTodayTimeEntry('time_out_am') !== 'Not recorded' || $this->getTodayTimeEntry('time_in_am') === 'Not recorded' ? 'disabled' : '' }}
            >
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time Out (AM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_out_am') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_out_am') }}
                </div>
            </button>

            <button
                id="timeInPmBtn"
                @class([
                    'w-full p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700',
                    'cursor-pointer' => $this->getTodayTimeEntry('time_in_pm') === 'Not recorded' && $this->isAfternoon(),
                    'cursor-not-allowed opacity-75' => $this->getTodayTimeEntry('time_in_pm') !== 'Not recorded' || !$this->isAfternoon(),
                ])
                {{ $this->getTodayTimeEntry('time_in_pm') !== 'Not recorded' || !$this->isAfternoon() ? 'disabled' : '' }}
            >
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time In (PM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_in_pm') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_in_pm') }}
                </div>
            </button>

            <button
                id="timeOutPmBtn"
                @class([
                    'w-full p-4 text-center bg-white rounded-lg shadow transition-colors duration-300 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700',
                    'cursor-pointer' => $this->getTodayTimeEntry('time_out_pm') === 'Not recorded' && $this->getTodayTimeEntry('time_in_pm') !== 'Not recorded',
                    'cursor-not-allowed opacity-75' => $this->getTodayTimeEntry('time_out_pm') !== 'Not recorded' || $this->getTodayTimeEntry('time_in_pm') === 'Not recorded',
                ])
                {{ $this->getTodayTimeEntry('time_out_pm') !== 'Not recorded' || $this->getTodayTimeEntry('time_in_pm') === 'Not recorded' ? 'disabled' : '' }}
            >
                <h2 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Time Out (PM)</h2>
                <div class="text-2xl font-bold {{ $this->getTodayTimeEntry('time_out_pm') !== 'Not recorded' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $this->getTodayTimeEntry('time_out_pm') }}
                </div>
            </button>
        </div>

    </div>

    <script>
        function getLocation() {
            // Show loading spinner
            const locationBtnText = document.getElementById('locationBtnText');
            const locationLoadingSpinner = document.getElementById('locationLoadingSpinner');
            const getLocationBtn = document.getElementById('getLocationBtn');

            locationBtnText.classList.add('hidden');
            locationLoadingSpinner.classList.remove('hidden');
            getLocationBtn.disabled = true;

            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Success callback
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Call Livewire method to update coordinates
                        window.Livewire.dispatch('set-coordinates', [latitude, longitude, false]);

                        // Reset button state
                        locationBtnText.classList.remove('hidden');
                        locationLoadingSpinner.classList.add('hidden');
                        getLocationBtn.disabled = false;
                        locationBtnText.textContent = 'Check Location';
                        getLocationBtn.classList.remove('bg-red-500', 'hover:bg-red-700');
                        getLocationBtn.classList.add('bg-green-500', 'hover:bg-green-700');
                    },
                    function(error) {
                        // Error callback
                        console.error('Error getting location:', error);
                        // Reset button state
                        locationBtnText.classList.remove('hidden');
                        locationLoadingSpinner.classList.add('hidden');
                        getLocationBtn.disabled = false;
                        locationBtnText.textContent = 'Retry Location';
                        getLocationBtn.classList.remove('bg-green-500', 'hover:bg-green-700');
                        getLocationBtn.classList.add('bg-red-500', 'hover:bg-red-700');
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                console.error('Geolocation is not supported');
                // Reset button state
                locationBtnText.classList.remove('hidden');
                locationLoadingSpinner.classList.add('hidden');
                getLocationBtn.disabled = false;
            }
        }

        // Get location when page loads
        document.addEventListener('DOMContentLoaded', function() {
            getLocation();
        });

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
            getLocation();

            // Disable button and show spinner
            getLocationBtn.disabled = true;
            locationBtnText.classList.add('invisible');
            locationLoadingSpinner.classList.remove('hidden');

            // Comprehensive logging function
            function logAndNotify(message, isError = false) {
                console.log(isError ? 'Location Error:' : 'Location Info:', message);

                // Use browser alert as a fallback if Filament notification fails
                alert(message);

                // Attempt Filament notification if available
                try {
                    if (isError) {
                        Notification.make()
                            .title('Location Error')
                            .body(message)
                            .danger()
                            .send();
                    } else {
                        Notification.make()
                            .title('Location Info')
                            .body(message)
                            .info()
                            .send();
                    }
                } catch (notificationError) {
                    console.error('Failed to send Filament notification:', notificationError);
                }
            }

            // Development mode bypass for secure context
            const isDevelopment = window.location.hostname === 'localhost' ||
                                  window.location.hostname === '127.0.0.1' ||
                                  window.location.hostname.includes('.local');

            // Check if the site is served over a secure context
            if (!window.isSecureContext && !isDevelopment) {
                logAndNotify('Geolocation requires a secure (HTTPS) connection. Please use HTTPS.', true);

                // Reset button state
                locationBtnText.classList.remove('invisible');
                locationLoadingSpinner.classList.add('hidden');
                getLocationBtn.disabled = false;
                return;
            }

            // Check if geolocation is supported
            if ('geolocation' in navigator) {
                // Verbose logging about geolocation support
                // logAndNotify('Geolocation is supported. Attempting to get location.');

                // Request location with maximum options
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Successfully got location
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        const accuracy = position.coords.accuracy;

                        // Log detailed location information
                        // logAndNotify(`Location found: Lat ${latitude}, Lng ${longitude}, Accuracy: ${accuracy}m`);

                        // Attempt to save location via Livewire
                        try {
                            @this.call('saveLocation', latitude, longitude)
                                .then(() => {
                                    // logAndNotify('Location saved successfully');
                                })
                                .catch((error) => {
                                    logAndNotify(`Failed to save location: ${error}`, true);
                                })
                                .finally(() => {
                                    // Reset button state
                                    locationBtnText.classList.remove('invisible');
                                    locationLoadingSpinner.classList.add('hidden');
                                    getLocationBtn.disabled = false;
                                });
                        } catch (livewireError) {
                            logAndNotify(`Livewire call failed: ${livewireError}`, true);

                            // Reset button state
                            locationBtnText.classList.remove('invisible');
                            locationLoadingSpinner.classList.add('hidden');
                            getLocationBtn.disabled = false;
                        }
                    },
                    function(error) {
                        // Location retrieval failed
                        let errorMessage = '';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "Location permission denied. Please enable location services in your browser or device settings.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Location information is unavailable. Check your device's location settings.";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "Location request timed out. Please try again.";
                                break;
                            default:
                                errorMessage = `Unknown location error: ${error.message}`;
                        }

                        // Log and notify about the error
                        logAndNotify(errorMessage, true);

                        // Reset button state
                        locationBtnText.classList.remove('invisible');
                        locationLoadingSpinner.classList.add('hidden');
                        getLocationBtn.disabled = false;
                    },
                    {
                        enableHighAccuracy: true,  // Request most accurate location
                        timeout: 30000,            // 30 seconds timeout
                        maximumAge: 0              // Don't use cached location
                    }
                );
            } else {
                // Geolocation not supported
                logAndNotify('Geolocation is not supported by your browser', true);

                // Reset button state
                locationBtnText.classList.remove('invisible');
                locationLoadingSpinner.classList.add('hidden');
                getLocationBtn.disabled = false;
            }
        });
            // Press and hold functionality
        function setupPressAndHold(buttonId, action, title) {
            const button = document.getElementById(buttonId);
            const modal = document.getElementById('confirmationModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('modalConfirmBtn');
            const cancelBtn = document.getElementById('modalCancelBtn');

            let pressTimer;
            let isPressed = false;

            function showModal() {
                modalTitle.textContent = title;
                modalMessage.textContent = 'Are you sure you want to record your time?';
                modal.classList.remove('hidden');

                return new Promise((resolve) => {
                    confirmBtn.onclick = () => {
                        modal.classList.add('hidden');
                        resolve(true);
                    };
                    cancelBtn.onclick = () => {
                        modal.classList.add('hidden');
                        resolve(false);
                    };
                });
            }

            function startPress() {
                if (button.disabled) return;

                isPressed = true;
                pressTimer = setTimeout(async () => {
                    if (isPressed) {
                        const confirmed = await showModal();
                        if (confirmed) {
                            @this[action]();
                        }
                    }
                }, 1000); // 1 second hold time
            }

            function endPress() {
                isPressed = false;
                clearTimeout(pressTimer);
            }

            // Mouse events
            button.addEventListener('mousedown', startPress);
            button.addEventListener('mouseup', endPress);
            button.addEventListener('mouseleave', endPress);

            // Touch events
            button.addEventListener('touchstart', (e) => {
                e.preventDefault();
                startPress();
            });
            button.addEventListener('touchend', endPress);
            button.addEventListener('touchcancel', endPress);
        }

        // Setup press and hold for all time buttons
        setupPressAndHold('timeInAmBtn', 'recordTimeInAm', 'Time In (AM)');
        setupPressAndHold('timeOutAmBtn', 'recordTimeOutAm', 'Time Out (AM)');
        setupPressAndHold('timeInPmBtn', 'recordTimeInPm', 'Time In (PM)');
        setupPressAndHold('timeOutPmBtn', 'recordTimeOutPm', 'Time Out (PM)');
    </script>
</x-filament-panels::page>
