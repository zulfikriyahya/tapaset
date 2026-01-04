<?php

namespace App\Filament\Resources\RfidLogResource\Pages;

use App\Filament\Resources\RfidLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRfidLogs extends ListRecords
{
    protected static string $resource = RfidLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
