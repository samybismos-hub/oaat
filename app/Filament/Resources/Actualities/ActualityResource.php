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
 * Documents & publications : les PDF que le visiteur peut télécharger depuis
 * le site (rapports, attestations, catalogues de projets…).
 */
class ActualityResource extends Resource
{
    protected static ?string $model = Actuality::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'Actualités';

    protected static ?string $modelLabel = 'Actualités';

    protected static ?string $pluralModelLabel = 'Actualités';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    // Le titre d'une fiche (fil d'Ariane, modale de suppression). Spatie renvoie
    // ici la traduction de la langue active.
    protected static ?string $recordTitleAttribute = 'title';

    /**
     * Compteur du menu : le nombre de documents réellement en ligne.
     * Le scope published() du modèle Document applique la même règle que le
     * site public (date de publication renseignée et déjà passée).
     */
    public static function getNavigationBadge(): ?string
    {
        $count = Actuality::query()->published()->count() ?? null;

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
