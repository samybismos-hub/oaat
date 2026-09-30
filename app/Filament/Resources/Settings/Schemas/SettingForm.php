<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

/**
 * Le formulaire des paramètres du site (une seule fiche).
 *
 * Trois types de colonnes y cohabitent, d'où trois outils différents :
 *   - des colonnes simples (« email »)                             → TextInput ;
 *   - des colonnes JSON traduisibles (« organisation_name » et
 *     « addresses », déclarées dans #[Translatable])               → Tabs FR/EN ;
 *   - des colonnes JSON non traduisibles (« phones », « socials ») → Repeater.
 */
class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Identité de l'organisation.
                Section::make('Identité de l\'organisation')
                    ->description('Le nom officiel et le logo, tels qu\'ils apparaissent dans l\'en-tête du site public.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('organisation_name.fr')
                            ->label('Nom de l\'organisation (FR)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('organisation_name.en')
                            ->label('Organisation name (EN)')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Adresse e-mail officielle')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Cette adresse est affichée sur la page « Contact » et utilisée pour le lien « Nous écrire ».')
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Logo')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans Setting::registerMediaCollections().
                            ->collection('logo')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('PNG à fond transparent de préférence, 2 Mo maximum. Un nouvel envoi remplace le logo précédent.')
                            ->columnSpanFull(),
                    ]),

                // 2. Les numéros de téléphone.
                //    La colonne "phones" est un tableau JSON de texte : le Repeater
                //    « simple » produit exactement cela, sans clé intermédiaire.
                Section::make('Téléphones')
                    ->description('Les numéros joignables, affichés sur la page « Contact ».')
                    ->schema([
                        Repeater::make('phones')
                            ->hiddenLabel()
                            // Par défaut, un Repeater démarre avec une ligne vide :
                            // ici, cela créerait un numéro fantôme. On part donc
                            // d'une liste vide, que l'utilisateur complète au besoin.
                            ->defaultItems(0)
                            ->simple(
                                TextInput::make('phone')
                                    ->label('Numéro')
                                    ->tel()
                                    ->placeholder('+243 000 000 000'),
                            )
                            ->addActionLabel('Ajouter un numéro')
                            ->helperText('Un numéro par ligne. Utilisez le format international (ex. : +243 993 537 325).'),
                    ]),

                // 3. Les adresses : elles se déclinent par langue, d'où les onglets.
                Section::make('Adresses')
                    ->description('Bureaux et siège social. Laissez un onglet vide si l\'information n\'existe que dans une langue.')
                    ->schema([
                        Tabs::make('Adresses')
                            ->tabs([
                                Tab::make('Français')
                                    ->icon('heroicon-m-language')
                                    ->schema([
                                        Repeater::make('addresses.fr')
                                            ->hiddenLabel()
                                            // Comme pour les téléphones, on part d'une
                                            // liste vide : une ligne vide créerait une
                                            // adresse fantôme dans le JSON.
                                            ->defaultItems(0)
                                            ->simple(
                                                Textarea::make('address')->label('Adresse')->rows(2),
                                            )
                                            ->addActionLabel('Ajouter une adresse'),
                                    ]),

                                Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->schema([
                                        Repeater::make('addresses.en')
                                            ->hiddenLabel()
                                            ->defaultItems(0)
                                            ->simple(
                                                Textarea::make('address')->label('Adresse')->rows(2),
                                            )
                                            ->addActionLabel('Add an address'),
                                    ]),
                            ]),
                    ]),

                // 4. Les réseaux sociaux.
                //    La colonne "socials" est un tableau JSON d'objets
                //    [{"platform": "Facebook", "url": "https://…"}] : c'est
                //    exactement la forme produite par les deux champs ci-dessous.
                Section::make('Réseaux sociaux')
                    ->description('Les pages officielles de l\'organisation. Laissez vide si aucun compte n\'est encore actif.')
                    ->schema([
                        Repeater::make('socials')
                            ->hiddenLabel()
                            // Sans ce réglage, le réseau social vide serait refusé
                            // à l'enregistrement (ses deux champs sont obligatoires).
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('platform')
                                    ->label('Réseau')
                                    ->datalist(['Facebook', 'LinkedIn', 'X (Twitter)', 'YouTube', 'Instagram', 'WhatsApp'])
                                    ->required(),

                                TextInput::make('url')
                                    ->label('Adresse de la page')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://…')
                                    ->required(),
                            ])
                            ->addActionLabel('Ajouter un réseau social'),
                    ]),

                // 5. Les chiffres clés affichés sur la page d'accueil.
                Section::make('Chiffres clés')
                    ->description('Ces valeurs alimentent les compteurs de la page d\'accueil. Tout champ laissé vide n\'est pas affiché : mieux vaut aucune valeur qu\'un chiffre non vérifié.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('stat_projects')
                            ->label('Projets réalisés')
                            ->numeric()
                            ->integer()
                            ->minValue(0),

                        TextInput::make('stat_beneficiaries')
                            ->label('Personnes bénéficiaires')
                            ->numeric()
                            ->integer()
                            ->minValue(0),

                        TextInput::make('stat_zones')
                            ->label('Zones d\'intervention')
                            ->numeric()
                            ->integer()
                            ->minValue(0),

                        TextInput::make('stat_years')
                            ->label('Années d\'expérience')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->helperText('Laissez vide : le site calcule automatiquement le nombre d\'années depuis la création (1995).'),
                    ]),
            ]);
    }
}
