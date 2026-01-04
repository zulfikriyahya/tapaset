<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceHistory extends EditRecord
{
    protected static string $resource = MaintenanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
