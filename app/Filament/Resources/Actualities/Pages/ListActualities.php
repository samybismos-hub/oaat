<?php

namespace App\Filament\Resources\Actualities\Pages;

use App\Filament\Resources\Actualities\ActualityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActualities extends ListRecords
{
    protected static string $resource = ActualityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
