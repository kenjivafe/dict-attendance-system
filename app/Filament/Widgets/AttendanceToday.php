<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AttendanceToday extends BaseWidget
{
    protected static ?string $heading = 'Attendance Today';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->whereDate('date', Carbon::today()) // Filter for today's date
                    ->orderByDesc('time_in_am') // Order by latest time-in
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Employee'),
                Tables\Columns\TextColumn::make('time_in_am')->time('h:i A')->label('AM Time-in'),
                Tables\Columns\TextColumn::make('time_out_am')->time('h:i A')->label('AM Time-out'),
                Tables\Columns\TextColumn::make('time_in_pm')->time('h:i A')->label('PM Time-in'),
                Tables\Columns\TextColumn::make('time_out_pm')->time('h:i A')->label('PM Time-out'),
            ]);
    }
}
