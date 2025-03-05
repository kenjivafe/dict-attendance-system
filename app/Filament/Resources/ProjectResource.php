<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'User Assignment';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Project Details')
                ->description(fn ($livewire) =>
                    $livewire instanceof Pages\EditProject
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
                        ->columnSpan(['default' => 2, 'sm' => 1]),
                    Forms\Components\Select::make('bureau_id')
                        ->native(false)
                        ->relationship('bureau', 'abbreviation')
                        ->required()
                        ->columnSpan(['default' => 2, 'sm' => 1]),
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
                    ->label('Project')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abbreviation')
                    ->searchable(),
            ])
            ->defaultGroup('bureau.name')
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
