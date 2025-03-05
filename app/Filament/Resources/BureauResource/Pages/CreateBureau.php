<?php

namespace App\Filament\Resources\BureauResource\Pages;

use App\Filament\Resources\BureauResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBureau extends CreateRecord
{
    protected static string $resource = BureauResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
