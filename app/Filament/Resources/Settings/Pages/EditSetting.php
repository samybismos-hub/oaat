<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Le formulaire unique des paramètres du site.
 *
 * Aucun bouton « Supprimer » n'est déclaré ici : la suppression de la fiche
 * est refusée par SettingResource::getDeleteAuthorizationResponse().
 */
class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    /**
     * Après enregistrement, on reste sur la page : l'utilisateur voit
     * immédiatement que ses modifications sont prises en compte, et peut
     * continuer à compléter les rubriques suivantes.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Paramètres du site enregistrés';
    }
}
