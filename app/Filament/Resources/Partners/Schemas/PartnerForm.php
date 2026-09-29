<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. L'identité du partenaire.
                //    Contrairement aux domaines et aux projets, le nom n'est PAS traduisible :
                //    « PNUD » ou « UNICEF » s'écrivent de la même façon en français et en
                //    anglais. Aucun onglet FR/EN ne se justifie donc ici.
                Section::make('Identité du partenaire')
                    ->description('Le nom officiel et le logo, tels qu\'ils apparaîtront sur le site public.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom du partenaire')
                            ->required()
                            ->maxLength(255)
                            // ignoreRecord : l'unicité ne doit pas bloquer la modification
                            // d'un partenaire qui garde son propre nom.
                            ->unique(ignoreRecord: true)
                            ->helperText('Ex. : PNUD, UNICEF, Louvain Développement.'),

                        // Le logo va dans la collection « logo » (singleFile : un seul à la fois,
                        // un nouvel envoi remplace le précédent).
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Logo')
                            ->collection('logo')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('PNG ou JPG, 2 Mo maximum. Un logo à fond transparent s\'intègre mieux au site.'),
                    ]),

                // 2. La présentation bilingue : ici « description » est bien traduisible,
                //    d'où les deux onglets (même logique que les domaines et les projets).
                Tabs::make('Traductions')
                    ->tabs([
                        Tab::make('Français')
                            ->icon('heroicon-m-language')
                            ->schema([
                                Textarea::make('description.fr')
                                    ->label('Présentation (FR)')
                                    ->rows(4)
                                    ->helperText('Quelques lignes sur le partenaire : son mandat, son domaine d\'expertise, son rôle auprès de l\'OAAT.'),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Textarea::make('description.en')
                                    ->label('Description (EN)')
                                    ->rows(4)
                                    ->helperText('Free translation of the French presentation.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
