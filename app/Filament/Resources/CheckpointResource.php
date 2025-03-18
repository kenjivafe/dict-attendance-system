<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckpointResource\Pages;
use App\Filament\Resources\CheckpointResource\RelationManagers;
use App\Filament\Resources\CheckpointResource\Widgets\CheckpointMap;
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

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Group::make([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Checkpoint Name'),
                    \Filament\Forms\Components\TextInput::make('lat')
                        ->label('Latitude')
                        ->readOnly(),
                    \Filament\Forms\Components\TextInput::make('lng')
                        ->label('Longitude')
                        ->readOnly(),
                ])->columnSpan(1),
                \Cheesegrits\FilamentGoogleMaps\Fields\Map::make('location')
                    ->label('Checkpoint Location')
                    ->mapControls([
                        'mapTypeControl'    => true,
                        'scaleControl'      => true,
                        'streetViewControl' => true,
                        'rotateControl'     => true,
                        'fullscreenControl' => true,
                        'searchBoxControl'  => false, // creates geocomplete field inside map
                        'zoomControl'       => false,
                    ])
                    ->height(fn () => '400px') // map height (width is controlled by Filament options)
                    ->defaultZoom(8) // default zoom level when opening form
                    ->defaultLocation([17.621510, 121.721800]) // default for new forms
                    ->draggable() // allow dragging to move marker
                    ->clickable(true) // allow clicking to move marker
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name'),
                \Filament\Tables\Columns\TextColumn::make('lat'),
                \Filament\Tables\Columns\TextColumn::make('lng'),
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

    public static function getWidgets(): array
    {
        return [
            CheckpointMap::class
        ];
    }
}
