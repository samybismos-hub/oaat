<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Models\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;


class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Contenu bilingue de la page (un onglet par langue)
                Tabs::make('Traductions')
                    ->tabs([
                        Tab::make('Français')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('title.fr')
                                    ->label('Titre de la page (FR)')
                                    ->required()
                                    ->maxLength(255),

                                RichEditor::make('body.fr')
                                    ->label('Contenu de la page (FR)')
                                    ->helperText('Mettez le texte en forme avec la barre d’outils (titres, gras, listes, liens). Le contenu est enregistré au format HTML, comme le texte déjà en place.'),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Page Title (EN)')
                                    ->maxLength(255),

                                RichEditor::make('body.en')
                                    ->label('Page Content (EN)')
                                    ->helperText('Si vous laissez ce champ vide, le contenu français sert de solution de repli.'),
                            ]),
                    ])
                    ->columnSpanFull(),
                // ─── Section réservée à la page Organisation (Historique / Reconnaissances / Zones) ─
                Section::make('Données structurées (page Organisation uniquement)')
                    ->description('Ces champs ne sont affichés que pour la page "Notre organisation". Remplissez les onglets Français ET Anglais.')
                    ->hidden(fn (?Page $record) => $record?->slug !== 'organisation')
                    ->schema([
                        // ─── Historique (timeline) ────────────────────────────────
                        Tabs::make('Historique')
                            ->tabs([
                                Tab::make('Français')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        Repeater::make('timeline.fr')
                                            ->label('Jalons historiques')
                                            ->schema([
                                                TextInput::make('year')
                                                    ->label('Année / Période')
                                                    ->required()
                                                    ->maxLength(20),
                                                TextInput::make('title')
                                                    ->label('Titre')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(3)
                                                    ->required(),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter un jalon'),
                                    ]),

                                Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->schema([
                                        Repeater::make('timeline.en')
                                            ->label('Historical milestones')
                                            ->schema([
                                                TextInput::make('year')
                                                    ->label('Year / Period')
                                                    ->required()
                                                    ->maxLength(20),
                                                TextInput::make('title')
                                                    ->label('Title')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(3)
                                                    ->required(),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Add a milestone'),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        // ─── Reconnaissances officielles ──────────────────────────
                        Tabs::make('Reconnaissances')
                            ->tabs([
                                Tab::make('Français')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        Repeater::make('recognitions.fr')
                                            ->label('Reconnaissances')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Titre')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('authority')
                                                    ->label('Autorité')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(2),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter une reconnaissance'),
                                    ]),

                                Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->schema([
                                        Repeater::make('recognitions.en')
                                            ->label('Recognitions')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Title')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('authority')
                                                    ->label('Authority')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(2),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Add a recognition'),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        // ─── Zones d'intervention ────────────────────────────────
                        Tabs::make('Zones')
                            ->tabs([
                                Tab::make('Français')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        Repeater::make('zones.fr')
                                            ->label("Zones d'intervention")
                                            ->schema([
                                                TextInput::make('province')
                                                    ->label('Province')
                                                    ->required()
                                                    ->datalist(['Sud-Kivu', 'Nord-Kivu'])
                                                    ->maxLength(50),
                                                TextInput::make('name')
                                                    ->label('Zone')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(2),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter une zone'),
                                    ]),

                                Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->schema([
                                        Repeater::make('zones.en')
                                            ->label('Areas of intervention')
                                            ->schema([
                                                TextInput::make('province')
                                                    ->label('Province')
                                                    ->required()
                                                    ->datalist(['Sud-Kivu', 'Nord-Kivu'])
                                                    ->maxLength(50),
                                                TextInput::make('name')
                                                    ->label('Area')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(2),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Add an area'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                // 2. Images du carrousel (héros) : plusieurs images possibles,
                //    visibles dans l'ordre de leur numéro de fichier.
                Section::make('Images du carrousel (héros)')
                    ->description('Les images défileront dans l\'ordre d\'envoi. Déposez-en plusieurs pour créer un carrousel, une seule pour un héro fixe. Si aucune image n\'est fournie, les anciens visuels fictifs apparaîtront en attendant.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero')
                            ->label('Images du carrousel')
                            ->collection('hero')
                            ->disk('public')
                            ->image()
                            ->multiple()
                            ->maxSize(5120)
                            ->helperText('JPEG, PNG ou WebP, 5 Mo maximum par fichier. Largeur minimale conseillée : 1920 px.'),
                    ]),

                // 3. Référencement (SEO) : textes destinés à Google et aux réseaux sociaux.
                Section::make('Référencement (SEO)')
                    ->description('Ces textes ne s\'affichent pas sur la page : ils servent aux moteurs de recherche (Google) et aux aperçus affichés lors d’un partage sur les réseaux sociaux. Si vous les laissez vides, le titre de la page et un extrait de son contenu sont utilisés automatiquement.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title.fr')
                            ->label('Titre SEO (FR)')
                            ->maxLength(70)
                            ->helperText('70 caractères maximum. Ex. : Aménagement des territoires en RD Congo | OAAT'),

                        TextInput::make('meta_title.en')
                            ->label('SEO Title (EN)')
                            ->maxLength(70),

                        Textarea::make('meta_description.fr')
                            ->label('Description SEO (FR)')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('160 caractères maximum : c\'est le résumé cliquable affiché sous le titre dans les résultats Google.'),

                        Textarea::make('meta_description.en')
                            ->label('SEO Description (EN)')
                            ->rows(3)
                            ->maxLength(160),
                    ]),

                // 3. Identifiant technique : l'adresse de la page sur le site public.
                Section::make('Identifiant technique')
                    ->description('Adresse de la page sur le site public. Sur les pages système (accueil, organisation), cet identifiant est verrouillé : le modifier casserait les liens existants et la navigation du site.')
                    ->schema([
                        TextInput::make('slug')
                            ->label('Identifiant URL (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Page $record): bool => (bool) $record?->isSystem())
                            ->helperText('Lettres minuscules, chiffres et tirets uniquement (ex. : a-propos, nos-partenaires).'),
                    ]),
            ]);
    }
}
