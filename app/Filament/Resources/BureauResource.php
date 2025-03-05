<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BureauResource\Pages;
use App\Filament\Resources\BureauResource\RelationManagers;
use App\Models\Bureau;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BureauResource extends Resource
{
    protected static ?string $model = Bureau::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup ='User Assignment';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bureau Details')
                    ->description(fn ($livewire) =>
                        $livewire instanceof Pages\EditBureau
                            ? 'This is the form section for the name, abbreviation and the head of the bureau.
                                Below the form, is the list of all the employees under this bureau.'
                            : 'This is the form section for the name, abbreviation and the head of the bureau.'
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
                    ->label('Bureau')
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
            RelationManagers\ProjectsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBureaus::route('/'),
            'create' => Pages\CreateBureau::route('/create'),
            'edit' => Pages\EditBureau::route('/{record}/edit'),
        ];
    }
}
