<?php

namespace App\Filament\Resources\BureauResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function form(Form $form): Form
    {
        return $form
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
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading('Projects')
            ->description('Manage the projects for this bureau.')
            ->columns([
                Tables\Columns\TextColumn::make('head'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('abbreviation'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\AssociateAction::make()
                        ->label('Existing Project')
                        ->preloadRecordSelect(),
                    Tables\Actions\CreateAction::make()
                        ->label('New Project'),
                ])->button()->label('Add Project')->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make()->label('Remove'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
