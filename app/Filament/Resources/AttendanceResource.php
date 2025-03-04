<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Attendance Records';

    protected static ?string $label = 'Attendance Record';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            DatePicker::make('date')->columnSpanFull(),
            TimePicker::make('time_in_am'),
            TimePicker::make('time_out_am'),
            TimePicker::make('time_in_pm'),
            TimePicker::make('time_out_pm'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(Auth::user()->hasRole(['super_admin', 'human_resource']) ? 'date' : 'date')
            ->groupingSettingsHidden()
            ->groups([
                Group::make(Auth::user()->hasRole(['super_admin', 'human_resource']) ? 'date' : 'date')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn ($record) => Auth::user()->hasRole(['super_admin', 'human_resource'])
                        ? $record->date->format('l, M d, Y') // Example: "Monday, Feb 19, 2025"
                        : $record->date->format('F Y') // Example: "February 2025"
                    )
                    ->orderQueryUsing(fn ($query) => Auth::user()->hasRole(['super_admin', 'human_resource'])
                        ? $query->orderBy('date', 'asc')
                        : $query->orderByRaw("DATE_FORMAT(date, '%Y-%m') ASC")
                    ),
            ])
            // ->paginated(['all'])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Employee')
                    ->visible(Auth::user()->hasRole(['super_admin', 'human_resource'])), // Only for Admin & HR

                TextColumn::make('date')
                    ->date('l, M d') // Example: "Monday, Feb 19, 2025"
                    ->label('Date')
                    ->visible(Auth::user()->hasRole('agent')), // Only for Agent

                TextColumn::make('time_in_am')->time('H:i')->label('AM Time-in'),
                TextColumn::make('time_out_am')->time('H:i')->label('AM Time-out'),
                TextColumn::make('time_in_pm')->time('H:i')->label('PM Time-in'),
                TextColumn::make('time_out_pm')->time('H:i')->label('PM Time-out'),
                TextColumn::make('undertime')
                    ->label('Undertime')
                // TextColumn::make('undertime_hours')->label('Undertime Hours'),
                // TextColumn::make('undertime_minutes')->label('Undertime Minutes'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->visible(Auth::user()->hasRole(['super_admin', 'human_resource']))
                    ->multiple()
                    ->label('Employee')
                    ->options(\App\Models\User::pluck('name', 'id')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Action::make('Time Out AM')
                    ->button()
                    ->label('Time-out AM')
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->hidden(fn ($record) => empty($record->time_in_am) || !empty($record->time_out_am))
                    ->action(function ($record) {
                        $record->update(['time_out_am' => now()]);

                        Notification::make()
                            ->title('AM Time-out recorded!')
                            ->success()
                            ->send();
                    }),

                Action::make('Time In PM')
                    ->button()
                    ->label('Time-in PM')
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->hidden(fn ($record) => empty($record->time_out_am) || !empty($record->time_in_pm))
                    ->action(function ($record) {
                        $record->update(['time_in_pm' => now()]);

                        Notification::make()
                            ->title('PM Time-in recorded!')
                            ->success()
                            ->send();
                    }),

                Action::make('Time Out PM')
                    ->button()
                    ->label('Time-out PM')
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->hidden(fn ($record) => empty($record->time_in_pm) || !empty($record->time_out_pm))
                    ->action(function ($record) {
                        $record->update(['time_out_pm' => now()]);

                        Notification::make()
                            ->title('PM Time-out recorded!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttendances::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->guard('web')->user();

        // If the user is an admin, show all records; otherwise, filter by trainer_id
        return parent::getEloquentQuery()->when(!$user->hasRole(['super_admin', 'human_resource']), function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orderByDesc('date'); // Order by latest date first;
    }
}
