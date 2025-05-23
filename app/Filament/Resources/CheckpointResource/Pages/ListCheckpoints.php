<?php

namespace App\Filament\Resources\CheckpointResource\Pages;

use App\Filament\Resources\CheckpointResource;
use App\Filament\Resources\CheckpointResource\Widgets\CheckpointMap;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCheckpoints extends ListRecords
{
    protected static string $resource = CheckpointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CheckpointMap::class,
        ];
    }
}
