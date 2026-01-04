<?php

namespace App\Filament\Resources\MaintenanceHistoryResource\Pages;

use App\Filament\Resources\MaintenanceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceHistories extends ListRecords
{
    protected static string $resource = MaintenanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
