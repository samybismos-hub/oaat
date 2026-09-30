<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Schemas\SettingForm;
use App\Models\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

/**
 * Paramètres du site : une seule fiche, celle des coordonnées officielles de
 * l'OAAT (nom, e-mail, téléphones, adresses, réseaux sociaux, chiffres clés).
 *
 * Il n'y a donc ni liste de plusieurs enregistrements, ni suppression :
 * la page d'accueil de la ressource ouvre directement le formulaire unique.
 */
class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $modelLabel = 'paramètre';

    protected static ?string $pluralModelLabel = 'paramètres';

    protected static string|\UnitEnum|null $navigationGroup = 'Organisation';

    protected static ?int $navigationSort = 2;

    /**
     * La fiche est unique : on ne peut en créer une seconde. Le bouton
     * « Créer » n'apparaît donc que si la table est encore vide (installation
     * neuve, avant exécution du seeder).
     */
    public static function canCreate(): bool
    {
        return Setting::query()->doesntExist();
    }

    /**
     * Les coordonnées officielles de l'organisation ne se suppriment pas :
     * sans elles, le site public n'a plus d'e-mail ni d'adresse à afficher.
     *
     * C'est bien cette méthode que Filament interroge pour décider d'afficher
     * (ou non) le bouton « Supprimer » et pour autoriser la suppression.
     */
    public static function getDeleteAuthorizationResponse(Model $record): Response
    {
        return Response::deny('Les paramètres du site ne peuvent pas être supprimés : ils contiennent les coordonnées officielles de l\'OAAT.');
    }

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
