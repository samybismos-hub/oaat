<?php

namespace App\Filament\Resources\Actualities\Schemas;

use App\Enums\ActualityType;
use App\Models\Actuality;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ActualityForm
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


                Section::make('Image de couverture')
                    ->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->label('Image de couverture')
                        // Le nom de la collection doit correspondre exactement à celui déclaré
                        // dans Actuality::registerMediaCollections().
                        ->collection('cover')
                        ->image()
                        ->maxSize(5120)
                        ->helperText("L'image affichée en tête de l'actualité. Une seule image possible : un nouvel envoi remplace l'ancienne."),
                    ]),
                Section::make('Publication')
                    ->schema([
                    DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->seconds(false)
                        ->displayFormat('d/m/Y H:i')
                        ->helperText("Laisser vide = brouillon (invisible sur le site). Une date future = actualité programmée, qui s'affichera d'elle-même le moment venu."),
                    ]),
                Section::make('Identifiant technique')
                    ->description('Adresse de la page sur le site public. Sur les pages système (accueil, organisation), cet identifiant est verrouillé : le modifier casserait les liens existants et la navigation du site.')
                    ->schema([
                        TextInput::make('slug')
                            ->label('Identifiant URL (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Actuality $record): bool => (bool) $record?->isSystem())
                            ->helperText('Adresse de l\'actualité sur le site public (ex: nos-actions-a-kindu). Modifier cet identifiant après publication casserait les liens déjà partagés.'),
                    ]),
            ]);
    }
}
