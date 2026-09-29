<?php

namespace App\Filament\Resources\Domains\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Zone de contenu traduisible avec onglets FR / EN
                Tabs::make('Traductions')
                    ->tabs([
                        Tab::make('Français')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('name.fr')
                                    ->label('Nom du domaine (FR)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                        // Ne génère le slug que lors de la création pour ne pas casser les URLs existantes
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                Textarea::make('objectives.fr')
                                    ->label('Objectifs (FR)')
                                    ->rows(4)
                                    ->helperText('Objectifs stratégiques poursuivis par l\'OAAT dans ce secteur.'),

                                Textarea::make('activities.fr')
                                    ->label('Activités principales (FR)')
                                    ->rows(4)
                                    ->helperText('Exemples concrets d\'interventions réalisées sur le terrain.'),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Domain Name (EN)')
                                    ->maxLength(255),

                                Textarea::make('objectives.en')
                                    ->label('Objectives (EN)')
                                    ->rows(4),

                                Textarea::make('activities.en')
                                    ->label('Key Activities (EN)')
                                    ->rows(4),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 2. Paramètres techniques du domaine
                Section::make('Configuration & Affichage')
                    ->description('Paramètres d\'identification et de positionnement du domaine sur le site public.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label('Identifiant URL (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Utilisé dans l\'URL du site (ex: /domaines/sante).'),

                        TextInput::make('icon')
                            ->label('Clé de l\'icône')
                            ->placeholder('ex: heart-pulse, book, leaf')
                            ->maxLength(100)
                            ->helperText('Identifiant d\'icône Lucide / FontAwesome affiché sur les cartes.'),

                        TextInput::make('position')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->helperText('Numéro d\'ordre (1 pour le premier affiché).'),
                    ]),
            ]);
    }
}
