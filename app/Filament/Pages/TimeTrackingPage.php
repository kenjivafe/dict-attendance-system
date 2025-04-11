<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class TimeTrackingPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $title = 'Time Tracking';

    protected static string $view = 'filament.pages.time-tracking-page';

    public $latitude = null;
    public $longitude = null;

    public function mount()
    {
        // Any initial setup can be done here
    }

    public function recordTimeEntry($type)
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

        // Create attendance record
        try {
            $attendance = Attendance::create([
                'user_id' => Auth::id(),
                'type' => $type,
                'time' => now(),
                'latitude' => $this->latitude,
                'longitude' => $this->longitude
            ]);

            Notification::make()
                ->title('Time Entry Recorded')
                ->body("Successfully recorded {$type}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Recording Time')
                ->body('Unable to record time entry: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function saveLocation($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        Notification::make()
            ->title('Location Saved')
            ->body("Latitude: {$latitude}, Longitude: {$longitude}")
            ->success()
            ->send();
    }
}
