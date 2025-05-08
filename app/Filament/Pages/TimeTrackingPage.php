<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Checkpoint;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;

class TimeTrackingPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $title = 'Time Trackings';

    protected static string $view = 'filament.pages.time-tracking-page';



    public $latitude = null;
    public $longitude = null;
    public $attendance = null;

    public function mount()
    {
        $this->attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();
    }

    #[On('set-coordinates')]
    public function setCoordinates($latitude, $longitude, $skipNotification = false)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        // Find the nearest checkpoint
        $this->findNearestCheckpoint($latitude, $longitude, $skipNotification);
    }

    private function validateTimeEntry($skipNotification = false)
    {
        if (!Auth::check()) {
            if (!$skipNotification) {
                Notification::make()
                    ->title('Authentication Required')
                    ->body('Please log in to record time.')
                    ->danger()
                    ->send();
            }
            return false;
        }

        if (!$this->latitude || !$this->longitude) {
            if (!$skipNotification) {
                Notification::make()
                    ->title('Location Required')
                    ->body('Please get your location before recording time.')
                    ->warning()
                    ->send();
            }
            return false;
        }

        $nearestCheckpoint = $this->findNearestCheckpoint($this->latitude, $this->longitude, $skipNotification);

        if (!$nearestCheckpoint) {
            Notification::make()
                ->title('Invalid Location')
                ->body('You are not near any registered checkpoint.')
                ->danger()
                ->send();
            return false;
        }

        return $nearestCheckpoint;
    }

    private function recordSpecificTime($timeField)
    {
        $nearestCheckpoint = $this->validateTimeEntry(true);
        if (!$nearestCheckpoint) {
            return;
        }

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => now()->toDateString(),
            ]
        );

        if ($attendance->$timeField !== null) {
            Notification::make()
                ->title('Already Recorded')
                ->body("Time for {$timeField} has already been recorded.")
                ->warning()
                ->send();
            return;
        }

        $attendance->$timeField = now();
        $attendance->save();

        // Update the local attendance property
        $this->attendance = $attendance;

        $timeFieldLabel = str_replace('_', ' ', $timeField);
        Notification::make()
            ->title('Time Entry Recorded')
            ->body("Successfully recorded {$timeFieldLabel} at {$nearestCheckpoint->name}")
            ->success()
            ->send();
    }

    public function recordTimeInAm()
    {
        $this->recordSpecificTime('time_in_am');
    }

    public function recordTimeOutAm()
    {
        $this->recordSpecificTime('time_out_am');
    }

    public function recordTimeInPm()
    {
        $this->recordSpecificTime('time_in_pm');
    }

    public function recordTimeOutPm()
    {
        $this->recordSpecificTime('time_out_pm');
    }

    public function getTodayTimeEntry($field)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($attendance && $attendance->$field) {
            return \Carbon\Carbon::parse($attendance->$field)->format('H:i:s');
        }

        return 'Not recorded';
    }

    private function determineNextTimeField($attendance)
    {
        $timeFields = [
            'time_in_am',
            'time_out_am',
            'time_in_pm',
            'time_out_pm'
        ];

        foreach ($timeFields as $field) {
            if (is_null($attendance->$field)) {
                return $field;
            }
        }

        return null;
    }

    public function getNextTimeEntryLabel()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();

        $timeFields = [
            'time_in_am' => 'Time In (AM)',
            'time_out_am' => 'Time Out (AM)',
            'time_in_pm' => 'Time In (PM)',
            'time_out_pm' => 'Time Out (PM)'
        ];

        foreach ($timeFields as $field => $label) {
            if (is_null($attendance?->$field)) {
                return $label;
            }
        }

        return 'All entries recorded';
    }

    private function findNearestCheckpoint($latitude, $longitude, $skipNotification = false)
    {
        // Earth's radius in kilometers
        $earthRadius = 6371;

        // Get all checkpoints
        $checkpoints = Checkpoint::whereNotNull('lat')->whereNotNull('lng')->get();

        // Prepare a log of checkpoint calculations
        $checkpointLog = [];

        // Find the nearest checkpoint within a reasonable distance
        $nearestCheckpoint = $checkpoints->map(function ($checkpoint) use ($latitude, $longitude, $earthRadius, &$checkpointLog) {
            // Haversine formula to calculate distance
            $latDiff = deg2rad($checkpoint->lat - $latitude);
            $lonDiff = deg2rad($checkpoint->lng - $longitude);
            $a = sin($latDiff/2) * sin($latDiff/2) +
                 cos(deg2rad($latitude)) * cos(deg2rad($checkpoint->lat)) *
                 sin($lonDiff/2) * sin($lonDiff/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distance = $earthRadius * $c * 1000; // Convert to meters

            // Calculate the absolute difference in coordinates
            $latDelta = abs($checkpoint->lat - $latitude);
            $lonDelta = abs($checkpoint->lng - $longitude);

            // Log detailed checkpoint information
            $checkpointLog[] = [
                'name' => $checkpoint->name,
                'checkpoint_lat' => $checkpoint->lat,
                'checkpoint_lng' => $checkpoint->lng,
                'user_lat' => $latitude,
                'user_lng' => $longitude,
                'distance' => $distance,
                'latDelta' => $latDelta,
                'lonDelta' => $lonDelta
            ];

            return [
                'checkpoint' => $checkpoint,
                'distance' => $distance,
                'latDelta' => $latDelta,
                'lonDelta' => $lonDelta
            ];
        })->filter(function ($item) {
            // Allow a more generous margin (within 500 meters)
            return $item['distance'] <= 500;
        })->sortBy('distance')->first();

        // Only show notification if not skipping
        if (!$skipNotification) {
            if ($nearestCheckpoint) {
                // Get address information
                $address = $this->getLocationAddress($latitude, $longitude);

                Notification::make()
                    ->title('Location Updated')
                    ->body(
                        "Nearest Checkpoint: {$nearestCheckpoint['checkpoint']->name}\n" .
                        "Distance: " . round($nearestCheckpoint['distance'], 2) . " meters\n" .
                        "Address: {$address}"
                    )
                    ->success()
                    ->send();
            } else {
                // If no checkpoint found, create a more descriptive notification
                $closestCheckpoint = collect($checkpointLog)->sortBy('distance')->first();
                
                Notification::make()
                    ->title('Location Too Far from Checkpoints')
                    ->body(
                        "You are not within 500 meters of any checkpoint.\n" .
                        "Closest Checkpoint: {$closestCheckpoint['name']}\n" .
                        "Distance: " . round($closestCheckpoint['distance'] / 1000, 2) . " km\n" .
                        "Your Location: Lat {$closestCheckpoint['user_lat']}, Lng {$closestCheckpoint['user_lng']}\n" .
                        "Checkpoint Location: Lat {$closestCheckpoint['checkpoint_lat']}, Lng {$closestCheckpoint['checkpoint_lng']}"
                    )
                    ->warning()
                    ->send();
            }
        }

        return $nearestCheckpoint ? $nearestCheckpoint['checkpoint'] : null;
    }

    private function getLocationAddress($latitude, $longitude)
    {
        // Use Google Geocoding API to get the address
        $apiKey = config('services.google.geocoding_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        try {
            $response = Http::get($url);
            $data = $response->json();

            if (!empty($data['results']) && isset($data['results'][0]['formatted_address'])) {
                return $data['results'][0]['formatted_address'];
            }
        } catch (\Exception $e) {
            // Ignore geocoding errors
        }

        return 'Unknown Location';
    }

    public function saveLocation($latitude, $longitude)
    {
        $this->setCoordinates($latitude, $longitude, true);
    }

    private function deg2rad($deg)
    {
        return $deg * pi() / 180;
    }
}
