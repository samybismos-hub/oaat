<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\Partners\Schemas\PartnerForm;
use App\Filament\Resources\Partners\Tables\PartnersTable;
use App\Models\Partner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    // 1. Le titre dans le menu de gauche
    protected static ?string $navigationLabel = 'Partenaires & bailleurs';

    // 2. Le label d'un enregistrement unique (ex. « Créer un partenaire »)
    protected static ?string $modelLabel = 'partenaire';

    // 3. Le label pluriel (ex. « Tous les partenaires »)
    protected static ?string $pluralModelLabel = 'partenaires';

    // 4. Le dossier/groupe dans le menu de gauche
    protected static string|\UnitEnum|null $navigationGroup = 'Partenaires';

    // 5. L'ordre du menu dans la barre latérale
    protected static ?int $navigationSort = 1;

    // 6. L'attribut utilisé comme titre d'une fiche. « name » est une vraie colonne :
    //    la recherche globale et le fil d'Ariane fonctionnent directement.
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Compteur affiché à côté du menu : le nombre de partenaires enregistrés.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = Partner::query()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function form(Schema $schema): Schema
    {
        return PartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnersTable::configure($table);
    }

    /**
     * Les onglets affichés sous la fiche : ici, les projets du partenaire et
     * le rôle qu'il y tient (bailleur / exécutant).
     */
    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }
}
