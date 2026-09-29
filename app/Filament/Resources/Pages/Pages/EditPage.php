<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Juste avant l'enregistrement, on réimpose le slug d'origine des pages
     * système. Le champ est déjà grisé dans le formulaire, mais un utilisateur
     * avancé peut contourner ce verrou depuis son navigateur : ce contrôle
     * côté serveur est la véritable protection.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->isSystem()) {
            $data['slug'] = $this->record->slug;
        }

        return $data;
    }
}
