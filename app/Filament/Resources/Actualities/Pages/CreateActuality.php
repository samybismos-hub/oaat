<?php

namespace App\Filament\Resources\Actualities\Pages;

use App\Filament\Resources\Actualities\ActualityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActuality extends CreateRecord
{
    protected static string $resource = ActualityResource::class;

    /**
     * Après création, on ouvre la fiche complète : c'est là que l'utilisateur
     * rédige le contenu et ajoute l'image de couverture, deux choses qui se font
     * mal dans une petite fenêtre de création.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
