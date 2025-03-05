<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Filament\Resources\OfficeResource\RelationManagers;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup ='User Assignment';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Office Details')
                ->description(fn ($livewire) =>
                    $livewire instanceof Pages\EditOffice
                        ? 'This is the form section for the name, abbreviation and the head of the office.
                            Below the form, is the list of all the employees under this office.'
                        : 'This is the form section for the name, abbreviation and the head of the office.'
                )
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(['default' => 2, 'sm' => 1,]),
                    Forms\Components\TextInput::make('abbreviation')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(['default' => 2, 'sm' => 1]),
                    Forms\Components\TextInput::make('head')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                ])
                ->aside('left')
                ->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('head')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Office')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abbreviation')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
