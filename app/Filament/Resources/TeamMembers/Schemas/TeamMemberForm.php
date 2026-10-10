<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Models\TeamMember;
use Closure;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
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

                        Toggle::make('is_founder')
                            ->label('Fondateur de l\'organisation')
                            ->live()
                            ->columnSpanFull()
                            ->helperText('Cochez pour le fondateur : sa biographie et sa citation deviennent éditables. Un seul fondateur est possible : si un autre membre est déjà fondateur, la sauvegarde sera refusée tant qu\'il n\'est pas décoché.')
                            ->rules([
                                fn (?TeamMember $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                    if ($value && TeamMember::query()
                                        ->where('is_founder', true)
                                        ->whereKeyNot($record?->getKey())
                                        ->exists()) {
                                        $fail('Un fondateur est déjà défini. Décochez-le d\'abord pour en désigner un autre.');
                                    }
                                },
                            ]),
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

                                Textarea::make('bio.fr')
                                    ->label('Biographie (FR)')
                                    ->rows(4)
                                    ->helperText('Parcours du membre, mission au sein de l\'OAAT.')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_founder'))
                                    ->dehydrated(fn (Get $get): bool => (bool) $get('is_founder')),

                                Textarea::make('quote.fr')
                                    ->label('Citation (FR)')
                                    ->rows(2)
                                    ->helperText('Une phrase mise en avant (utilisée notamment pour le fondateur).')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_founder'))
                                    ->dehydrated(fn (Get $get): bool => (bool) $get('is_founder')),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('role.en')
                                    ->label('Position (EN)')
                                    ->maxLength(255),

                                Textarea::make('bio.en')
                                    ->label('Biography (EN)')
                                    ->rows(4)
                                    ->helperText('Background of the member, role within OAAT.')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_founder'))
                                    ->dehydrated(fn (Get $get): bool => (bool) $get('is_founder')),

                                Textarea::make('quote.en')
                                    ->label('Quote (EN)')
                                    ->rows(2)
                                    ->helperText('A sentence highlighted (used notably for the founder).')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_founder'))
                                    ->dehydrated(fn (Get $get): bool => (bool) $get('is_founder')),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 3. Le portrait (celui du fondateur, marqué « is_founder », est
                //    mis en avant sur la page d'accueil via _about).
                Section::make('Portrait')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->label('Portrait')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans TeamMember::registerMediaCollections().
                            ->collection('photo')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('Portrait carré (JPG ou PNG, 2 Mo maximum). Celui du fondateur apparaît sur la page d\'accueil.'),
                    ]),
            ]);
    }
}
