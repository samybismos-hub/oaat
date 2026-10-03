<?php

namespace App\Filament\Resources\Actualities;

use App\Filament\Resources\Actualities\Pages\CreateActuality;
use App\Filament\Resources\Actualities\Pages\EditActuality;
use App\Filament\Resources\Actualities\Pages\ListActualities;
use App\Filament\Resources\Actualities\Schemas\ActualityForm;
use App\Filament\Resources\Actualities\Tables\ActualitiesTable;
use App\Models\Actuality;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Actualités : les nouvelles publiées sur le site (vie de l'association,
 * appels à financement, communiqués). Une actualité est un article daté :
 * le site public ne l'affiche qu'à partir de sa date de publication.
 */
class ActualityResource extends Resource
{
    protected static ?string $model = Actuality::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'Actualités';

    // Filament assemble ses libellés (« Créer un(e) … », « … supprimée ») à
    // partir de ces deux mots : ils doivent rester au singulier et au pluriel
    // naturels, sans majuscule (le libellé du menu, lui, est navigationLabel).
    protected static ?string $modelLabel = 'actualité';

    protected static ?string $pluralModelLabel = 'actualités';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    // Le titre d'une fiche (fil d'Ariane, modale de suppression). Spatie renvoie
    // ici la traduction de la langue active.
    protected static ?string $recordTitleAttribute = 'title';

    /**
     * Compteur du menu : le nombre d'actualités réellement en ligne.
     * Le scope published() du modèle Actuality applique la même règle que le
     * site public : date de publication renseignée ET déjà passée. Les
     * brouillons et les actualités programmées ne sont donc pas comptés.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = Actuality::query()->published()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return ActualityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActualitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActualities::route('/'),
            'create' => CreateActuality::route('/create'),
            'edit' => EditActuality::route('/{record}/edit'),
        ];
    }
}
