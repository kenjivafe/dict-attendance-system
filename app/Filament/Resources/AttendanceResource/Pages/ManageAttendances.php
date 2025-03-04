<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageAttendances extends ManageRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        // Check if an attendance record exists for today
        $hasAttendanceToday = Attendance::whereDate('date', today())
            ->where('user_id', Auth::id()) // Assuming attendance is per user
            ->exists();

        return $hasAttendanceToday ? [] : [
            Actions\Action::make('Time In Today')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->action(function () {
                    Attendance::create([
                        'user_id' => Auth::id(),
                        'date' => today(),
                        'time_in_am' => now(),
                    ]);

                    // Send Filament success notification
                    Notification::make()
                        ->title('Time-in recorded successfully!')
                        ->success()
                        ->send();

                    // Refresh the page using JavaScript
                    return redirect(request()->header('Referer'));
                })
        ];
    }
}
