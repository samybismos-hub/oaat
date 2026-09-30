<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Schemas\DocumentForm;
use App\Filament\Resources\Documents\Tables\DocumentsTable;
use App\Models\Document;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Documents & publications : les PDF que le visiteur peut télécharger depuis
 * le site (rapports, attestations, catalogues de projets…).
 */
class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Documents & publications';

    protected static ?string $modelLabel = 'document';

    protected static ?string $pluralModelLabel = 'documents';

    protected static string|\UnitEnum|null $navigationGroup = 'Médias & documents';

    protected static ?int $navigationSort = 2;

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
        $count = Document::query()->published()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
