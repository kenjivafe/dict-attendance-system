<?php

namespace App\Filament\Resources\BureauResource\Pages;

use App\Filament\Resources\BureauResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBureau extends EditRecord
{
    protected static string $resource = BureauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
