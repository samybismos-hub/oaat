<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Albums\Schemas\AlbumForm;
use App\Models\Album;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * L'onglet « Albums photo » d'une fiche projet : la galerie du projet.
 *
 * C'est le seul endroit où se téléversent les photos d'un projet — l'ancien
 * champ « Galerie photos » du formulaire (collection « photos » du projet
 * lui-même) a été supprimé car rien ne le lisait. Chaque album regroupe des
 * photos sous un titre, et tous les albums rattachés au projet alimentent la
 * section Galerie de la fiche publique (pages.projets-show). Les albums
 * généraux du site, eux, se gèrent dans la ressource « Albums photo » du menu
 * « Médias & documents ».
 */
class AlbumsRelationManager extends RelationManager
{
    protected static string $relationship = 'albums';

    protected static ?string $title = 'Albums photo';

    /**
     * Le formulaire des modals « Créer / Modifier » : les mêmes champs que la
     * ressource « Albums photo », sans le menu « Projet associé » — le projet
     * est déjà imposé par la fiche sur laquelle s'ouvre cet onglet.
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components(AlbumForm::components(avecRattachementProjet: false));
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photos')
                    ->label('Aperçu')
                    ->collection('photos')
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(40)
                    ->width(64),

                TextColumn::make('title_fr')
                    ->label('Album')
                    ->state(fn (Album $record): string => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->weight('bold')
                    ->limit(60),

                TextColumn::make('media_count')
                    ->label('Photos')
                    ->counts('media')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'gray' : 'warning')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Créer un album')
                    ->modalHeading('Créer un album pour ce projet')
                    ->modalWidth('3xl'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
