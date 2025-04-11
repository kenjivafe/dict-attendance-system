<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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

    public function recordTimeEntry()
    {
        // Validate that a user is logged in
        if (!Auth::check()) {
            Notification::make()
                ->title('Authentication Required')
                ->body('Please log in to record time.')
                ->danger()
                ->send();
            return;
        }

        // Find or create today's attendance record
        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => now()->toDateString(),
            ]
        );

        // Determine which time field to update
        $timeFieldToUpdate = $this->determineNextTimeField($attendance);

        if ($timeFieldToUpdate) {
            $attendance->$timeFieldToUpdate = now();
            $attendance->save();

            // Update the local attendance property
            $this->attendance = $attendance;

            Notification::make()
                ->title('Time Entry Recorded')
                ->body("Successfully recorded {$timeFieldToUpdate}")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Time Entry Error')
                ->body('All time entries for today have been recorded.')
                ->warning()
                ->send();
        }
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

    public function saveLocation($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        // Use Google Geocoding API to get the address
        $apiKey = config('services.google.geocoding_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        try {
            $response = Http::get($url);
            $data = $response->json();

            $address = 'Unknown Location';
            if (!empty($data['results']) && isset($data['results'][0]['formatted_address'])) {
                $address = $data['results'][0]['formatted_address'];
            }

            Notification::make()
                ->title('You are in: ' . $address)
                ->body("Latitude: {$latitude}, Longitude: {$longitude}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Fallback to default notification if geocoding fails
            Notification::make()
                ->title('Location Saved')
                ->body("Latitude: {$latitude}, Longitude: {$longitude}")
                ->warning()
                ->send();
        }
    }
}
