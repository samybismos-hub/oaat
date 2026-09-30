<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * Cette liste ne sert qu'une fraction de seconde : il n'existe qu'une seule
 * ligne de paramètres, on ouvre donc directement son formulaire.
 *
 * (Filament n'a pas de notion de « ressource à enregistrement unique » : ce
 * détour par la liste est le moyen le plus simple d'y parvenir, sans écrire
 * une page sur mesure avec sa propre vue Blade.)
 */
class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    public function mount(): void
    {
        parent::mount();

        $setting = Setting::current();

        if ($setting) {
            $this->redirect(EditSetting::getUrl(['record' => $setting]));

            return;
        }

        // Aucun enregistrement (installation neuve) : on reste sur la liste,
        // qui propose alors le bouton « Créer » (voir SettingResource::canCreate()).
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
