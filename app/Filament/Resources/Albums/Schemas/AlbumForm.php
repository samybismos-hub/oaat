<?php

namespace App\Filament\Resources\Albums\Schemas;

use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AlbumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Le nom de l'album. Comme pour les documents, un seul champ est
                //    traduisible : les deux langues tiennent donc côte à côte.
                Section::make('Album')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title.fr')
                            ->label('Nom de l\'album (FR)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ex. : Construction de l\'école de Karhale.'),

                        TextInput::make('title.en')
                            ->label('Album name (EN)')
                            ->maxLength(255),

                        Select::make('project_id')
                            ->label('Projet associé (facultatif)')
                            ->relationship('project', 'title')
                            // La colonne "title" des projets contient du JSON ({"fr": …, "en": …}) :
                            // sans cette ligne, le menu déroulant afficherait le JSON brut.
                            // (On renonce volontairement à ->searchable() : la recherche irait
                            // chercher dans la colonne JSON.)
                            ->getOptionLabelFromRecordUsing(fn (Project $record): string => $record->getTranslation('title', 'fr'))
                            ->preload()
                            ->native(false)
                            ->helperText('Un album rattaché à un projet apparaît sur la fiche de ce projet. Laissez vide pour un album général du site.')
                            ->columnSpanFull(),
                    ]),

                // 2. Les photos de l'album.
                Section::make('Photos de l\'album')
                    ->description('Conseils : JPG ou PNG, 1600 px de large minimum, moins de 5 Mo par image.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photos')
                            ->label('Photos')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans Album::registerMediaCollections().
                            ->collection('photos')
                            ->image()
                            ->multiple()
                            // L'ordre choisi ici est celui de la galerie sur le site public.
                            ->reorderable()
                            ->maxFiles(48)
                            ->maxSize(5120)
                            ->helperText('Glissez-déposez les vignettes pour choisir l\'ordre d\'affichage dans la galerie.'),
                    ]),
            ]);
    }
}
