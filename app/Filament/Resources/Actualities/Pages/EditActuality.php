<?php

namespace App\Filament\Resources\Actualities\Pages;

use App\Filament\Resources\Actualities\ActualityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditActuality extends EditRecord
{
    protected static string $resource = ActualityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
