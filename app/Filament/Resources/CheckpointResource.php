<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckpointResource\Pages;
use App\Filament\Resources\CheckpointResource\RelationManagers;
use App\Models\Checkpoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CheckpointResource extends Resource
{
    protected static ?string $model = Checkpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Checkpoint Name'),
                    \Filament\Forms\Components\TextInput::make('latitude')
                        ->readOnly(),
                    \Filament\Forms\Components\TextInput::make('longitude')
                        ->readOnly(),
                ])->columnSpan(1),
                \Dotswan\MapPicker\Fields\Map::make('shapes')
                    ->label('Checkpoint Area')
                    // ->defaultLocation(latitude: 17.621678353601, longitude: 121.72208070651)
                    ->clickable(true)
                    ->geoMan(true)
                    ->drawCircle(true)
                    ->drawPolygon(true)
                    ->boundaries(true, 15.8, 120.3, 21.2, 122.5)
                    ->minZoom(7)
                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                        if ($state) {
                            $set('latitude', $state['lat']);
                            $set('longitude', $state['lng']);
                            // $set('shapes', $state['shapes'] ?? []);
                        }
                    })
                    ->afterStateHydrated(function ($state, ?object $record, Set $set): void {
                        if ($record) {
                            $set('location', [
                                'lat' => $record->latitude ?? null,
                                'lng' => $record->longitude ?? null,
                                // 'shapes' => $record->shapes ?? [],
                            ]);
                        }
                    })
                    ->showMarker(true)
                    ->clickable(true)
                    // ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                    ->extraStyles([
                        'min-height: 50vh',
                        'border-radius: 10px'
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('latitude'),
                \Filament\Tables\Columns\TextColumn::make('longitude'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckpoints::route('/'),
            'create' => Pages\CreateCheckpoint::route('/create'),
            'edit' => Pages\EditCheckpoint::route('/{record}/edit'),
        ];
    }
}
