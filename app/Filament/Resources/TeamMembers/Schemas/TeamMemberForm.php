<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. L'identité du membre. Le nom n'est pas traduisible : un nom
                //    propre s'écrit de la même façon dans les deux langues.
                Section::make('Membre de l\'équipe')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ex. : Ir MANEMA CIRHAHINGIRWA Roger.'),

                        TextInput::make('position')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->helperText('Les membres sont affichés du plus petit au plus grand numéro (1 = en tête de liste).'),
                    ]),

                // 2. La fonction, elle, se traduit : « Représentant national »
                //    devient « National Representative » sur le site en anglais.
                Tabs::make('Traductions')
                    ->tabs([
                        Tab::make('Français')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('role.fr')
                                    ->label('Fonction (FR)')
                                    ->maxLength(255)
                                    ->helperText('Ex. : Représentant national, Chargé de programmes.'),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('role.en')
                                    ->label('Position (EN)')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 3. Le portrait.
                Section::make('Portrait')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->label('Photo')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans TeamMember::registerMediaCollections().
                            ->collection('photo')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('JPG ou PNG, 2 Mo maximum. Un portrait carré (par exemple 500 × 500 px) s\'intègre mieux aux fiches de l\'équipe.'),
                    ]),
            ]);
    }
}
