<?php

namespace App\Filament\Resources\Actualities\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ActualityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Contenu bilingue de l'actualité : un onglet par langue.
                Tabs::make('Traductions')
                    ->tabs([
                        Tab::make('Français')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('title.fr')
                                    ->label('Titre de l\'actualité (FR)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                        // Le slug n'est proposé qu'à la création : le changer
                                        // sur une actualité déjà diffusée casserait les liens
                                        // partagés (le site public adresse les articles par ce slug).
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                RichEditor::make('body.fr')
                                    ->label('Contenu de l\'actualité (FR)')
                                    ->helperText('Mettez le texte en forme avec la barre d’outils (titres, gras, listes, liens). Le contenu est enregistré au format HTML, comme le texte déjà en place.'),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title (EN)')
                                    ->maxLength(255),

                                RichEditor::make('body.en')
                                    ->label('Content (EN)')
                                    ->helperText('Si vous laissez ce champ vide, le contenu français sert de solution de repli.'),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 2. L'image de couverture : une seule par actualité.
                Section::make('Image de couverture')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Image de couverture')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans Actuality::registerMediaCollections().
                            ->collection('cover')
                            ->image()
                            ->maxSize(5120)
                            ->helperText("L'image affichée en tête de l'actualité. Une seule image possible : un nouvel envoi remplace l'ancienne."),
                    ]),

                // 3. La publication : brouillon, programmée ou en ligne.
                Section::make('Publication')
                    ->schema([
                        DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText("Laisser vide = brouillon (invisible sur le site). Une date future = actualité programmée, qui s'affichera d'elle-même le moment venu."),
                    ]),

                // 4. Le slug : l'adresse de l'actualité sur le site public.
                Section::make('Identifiant technique')
                    ->description("Adresse de l'actualité sur le site public. Elle est proposée automatiquement à partir du titre français : ne la corrigez que si elle ne vous convient pas.")
                    ->schema([
                        TextInput::make('slug')
                            ->label('Identifiant URL (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Ex. : nos-actions-a-kindu. Modifier cet identifiant après publication casserait les liens déjà partagés.'),
                    ]),
            ]);
    }
}
