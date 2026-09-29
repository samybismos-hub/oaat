<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Filament\Resources\Pages\Tables\PagesTable;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack; // OutlinedDocumentText

    // 1. Navigation & Labels
    protected static ?string $navigationLabel = 'Pages du site';

    protected static ?string $modelLabel = 'page';

    protected static ?string $pluralModelLabel = 'pages';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenu Institutionnel';

    protected static ?int $navigationSort = 1;

    /**
     * Les pages ne sont pas créées depuis l'administration : elles
     * correspondent à des rubriques fixes du site (voir PageSeeder).
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Les pages système (accueil, organisation) ne peuvent pas être supprimées :
     * leur contenu est la vitrine institutionnelle de l'OAAT.
     *
     * Attention : c'est bien cette méthode que Filament interroge pour décider
     * d'afficher (ou non) le bouton "Supprimer" et pour autoriser la
     * suppression. Surcharger `canDelete()` ne suffirait pas.
     */
    public static function getDeleteAuthorizationResponse(Model $record): Response
    {
        if ($record instanceof Page && $record->isSystem()) {
            return Response::deny('Cette page fait partie du contenu institutionnel du site : sa suppression est désactivée.');
        }

        return parent::getDeleteAuthorizationResponse($record);
    }

    /**
     * Aucune suppression en masse sur les pages : une mauvaise sélection
     * viderait le contenu du site d'un seul clic.
     */
    public static function getDeleteAnyAuthorizationResponse(): Response
    {
        return Response::deny('La suppression en masse est désactivée sur les pages du site.');
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
