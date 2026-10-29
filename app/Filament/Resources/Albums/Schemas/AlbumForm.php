<?php

namespace App\Filament\Resources\Albums\Schemas;

use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AlbumForm
{
    /**
     * Les champs d'un album, réutilisés à deux endroits :
     *  - la ressource « Albums photo » (rattachement à un projet possible) ;
     *  - l'onglet « Albums photo » de la fiche projet (rattachement déjà imposé,
     *    le menu « Projet associé » est alors inutile).
     *
     * @param  bool  $avecRattachementProjet  ajoute le menu « Projet associé »
     */
    public static function components(bool $avecRattachementProjet = true): array
    {
        $champsAlbum = [
            // Le nom de l'album. Comme pour les documents, un seul champ est
            // traduisible : les deux langues tiennent donc côte à côte.
            TextInput::make('title.fr')
                ->label('Nom de l\'album (FR)')
                ->required()
                ->maxLength(255)
                ->helperText('Ex. : Construction de l\'école de Karhale.')
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                    // Le slug n'est proposé qu'à la création : le modifier sur un
                    // album existant casserait les adresses déjà diffusées.
                    if ($operation === 'create' && filled($state)) {
                        $set('slug', Str::slug($state));
                    }
                }),

            TextInput::make('title.en')
                ->label('Album name (EN)')
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Identifiant URL (slug)')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Adresse de l\'album sur la page Galerie (ex. : construction-ecole-karhale).'),
        ];

        if ($avecRattachementProjet) {
            $champsAlbum[] = Select::make('project_id')
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
                ->columnSpanFull();
        }

        return [
            Section::make('Album')
                ->columns(2)
                ->schema($champsAlbum),

            // 2. Les photos de l'album.
            Section::make('Photos de l\'album')
                ->description('Conseils : JPG ou PNG, 1600 px de large minimum, moins de 5 Mo par image.')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('photos')
                        ->label('Photos')
                        // Le nom de la collection doit correspondre exactement à celui
                        // déclaré dans Album::registerMediaCollections().
                        ->collection('photos')
                        ->disk('public')
                        ->image()
                        ->multiple()
                        // L'ordre choisi ici est celui de la galerie sur le site public.
                        ->reorderable()
                        ->maxFiles(48)
                        ->maxSize(5120)
                        ->helperText('Glissez-déposez les vignettes pour choisir l\'ordre d\'affichage dans la galerie.'),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::components(avecRattachementProjet: true));
    }
}
