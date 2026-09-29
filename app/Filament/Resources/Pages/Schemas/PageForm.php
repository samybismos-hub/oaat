<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Models\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

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

                // 2. Référencement (SEO) : textes destinés à Google et aux réseaux sociaux.
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
