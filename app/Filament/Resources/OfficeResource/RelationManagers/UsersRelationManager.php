<?php

namespace App\Filament\Resources\OfficeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email(),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->password(),
                Forms\Components\Select::make('roles')
                    ->required()
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                // TextColumn::make('project.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        default => 'primary',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\AssociateAction::make()
                        ->label('Existing Employee')
                        ->preloadRecordSelect(),
                    Tables\Actions\CreateAction::make()
                        ->label('New Employee'),
                ])->button()->label('Add Employee')->icon('heroicon-o-plus'),
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
