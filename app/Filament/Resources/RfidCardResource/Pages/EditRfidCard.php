<?php

namespace App\Filament\Resources\RfidCardResource\Pages;

use App\Filament\Resources\RfidCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRfidCard extends EditRecord
{
    protected static string $resource = RfidCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
