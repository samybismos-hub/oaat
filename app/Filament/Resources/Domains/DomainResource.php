<?php

namespace App\Filament\Resources\Domains;

use App\Filament\Resources\Domains\Pages\CreateDomain;
use App\Filament\Resources\Domains\Pages\EditDomain;
use App\Filament\Resources\Domains\Pages\ListDomains;
use App\Filament\Resources\Domains\Schemas\DomainForm;
use App\Filament\Resources\Domains\Tables\DomainsTable;
use App\Models\Domain;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    // 1. Le titre dans le menu de gauche
    protected static ?string $navigationLabel = 'Domaines d\'intervention';

    // 2. Le label d'un enregistrement unique (ex: "Créer un Domaine d’intervention")
    protected static ?string $modelLabel = 'créer un domaine d\'intervention';

    // 3. Le label pluriel (ex: "Tous les Domaines d’intervention")
    protected static ?string $pluralModelLabel = 'domaines d\'intervention';

    // 5. Le dossier/groupe dans le menu
    protected static string|\UnitEnum|null $navigationGroup = 'Structure & Programmes';

    // 6. L'ordre du menu dans la barre latérale
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DomainForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DomainsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomains::route('/'),
            'create' => CreateDomain::route('/create'),
            'edit' => EditDomain::route('/{record}/edit'),
        ];
    }
}
