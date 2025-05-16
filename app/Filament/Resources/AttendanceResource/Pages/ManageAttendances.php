<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
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

        $actions = [];

        // Only add create action for admins
        if (Auth::user()->hasRole(['super_admin', 'human_resource'])) {
            $actions[] = Actions\CreateAction::make()
                ->form([
                    Select::make('user_id')
                        ->label('Employee')
                        ->options(\App\Models\User::pluck('name', 'id')->toArray())
                        ->required()
                        ->live()
                        ->searchable(),
                    DatePicker::make('date')
                        ->label('Date')
                        ->native(false)
                        ->required()
                        ->disabledDates(function (callable $get) {
                            $userId = $get('user_id');
                            if (!$userId) return [];

                            // Format dates explicitly
                            return Attendance::where('user_id', $userId)
                                ->get()
                                ->map(fn($attendance) => $attendance->date->format('Y-m-d'))
                                ->toArray();
                        }),
                    TimePicker::make('time_in_am'),
                    TimePicker::make('time_out_am'),
                    TimePicker::make('time_in_pm'),
                    TimePicker::make('time_out_pm'),
                ]);
        }

        // Add Time In Today action for non-admins
        if (!$hasAttendanceToday) {
            $actions[] = Actions\Action::make('Time In Today')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->modalContent(fn () => view('livewire.location-tracker'))
                ->modalSubmitAction(false)
                ->modalCancelAction(false);
        }

        return $actions;
    }
}
