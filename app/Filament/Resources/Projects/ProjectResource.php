<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\PartnersRelationManager;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    // 1. Le titre dans le menu de gauche
    protected static ?string $navigationLabel = 'Projets & réalisations';

    // 2. Le label d'un enregistrement unique (ex: « Créer un projet »)
    protected static ?string $modelLabel = 'projet';

    // 3. Le label pluriel (ex: « Tous les projets »)
    protected static ?string $pluralModelLabel = 'projets';

    // 4. Le dossier/groupe dans le menu (le même que les domaines)
    protected static string|\UnitEnum|null $navigationGroup = 'Structure & Programmes';

    // 5. L'ordre du menu dans la barre latérale
    protected static ?int $navigationSort = 2;

    // 6. L'attribut utilisé comme titre d'une fiche (fil d'Ariane, recherche globale).
    //    Le générateur avait écrit « Project » : c'est une colonne inexistante.
    protected static ?string $recordTitleAttribute = 'title';

    /**
     * Le domaine de chaque projet est chargé en une seule requête supplémentaire,
     * au lieu d'une requête par ligne affichée (problème dit « N+1 »).
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('domain');
    }

    /**
     * Compteur affiché à côté du menu : le nombre de fiches publiées.
     * Il s'appuie sur le scope published() défini dans le modèle Project.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = Project::query()->published()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    /**
     * Les onglets affichés sous la fiche projet : les partenaires et leur rôle
     * (bailleur / exécutant), stocké dans la table pivot.
     */
    public static function getRelations(): array
    {
        return [
            PartnersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
