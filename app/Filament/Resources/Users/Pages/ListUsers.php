<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\UserExporter;
use Filament\Actions\ExportAction;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),


              ExportAction::make()
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->exporter(UserExporter::class),
        ];
    }
}
