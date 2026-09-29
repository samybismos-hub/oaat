<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Models\Domain;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Contenu bilingue : un onglet par langue (comme pour les domaines)
                Tabs::make('Traductions')
                    ->tabs([
                        Tab::make('Français')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('title.fr')
                                    ->label('Titre du projet (FR)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                                        // Le slug n'est proposé qu'à la création : le modifier
                                        // sur un projet existant casserait les liens déjà diffusés.
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                Textarea::make('objectives.fr')
                                    ->label('Objectifs (FR)')
                                    ->rows(4)
                                    ->helperText('Ce que le projet cherchait à changer sur le terrain.'),

                                Textarea::make('beneficiaries.fr')
                                    ->label('Bénéficiaires (FR)')
                                    ->rows(3)
                                    ->helperText('Qui a bénéficié du projet (ex. : 15 800 personnes).'),

                                Textarea::make('results.fr')
                                    ->label('Résultats obtenus (FR)')
                                    ->rows(3)
                                    ->helperText('Ce qui a été livré ou construit, en une ou deux phrases.'),
                            ]),

                        Tab::make('English')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Project Title (EN)')
                                    ->maxLength(255),

                                Textarea::make('objectives.en')
                                    ->label('Objectives (EN)')
                                    ->rows(4),

                                Textarea::make('beneficiaries.en')
                                    ->label('Beneficiaries (EN)')
                                    ->rows(3),

                                Textarea::make('results.en')
                                    ->label('Results (EN)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 2. À quel domaine le projet appartient-il, et à quelle adresse ?
                Section::make('Rattachement & identification')
                    ->description('Un projet appartient toujours à un domaine d\'intervention et possède sa propre adresse sur le site public.')
                    ->columns(2)
                    ->schema([
                        Select::make('domain_id')
                            ->label('Domaine d\'intervention')
                            ->relationship('domain', 'name')
                            // La colonne "name" des domaines contient du JSON ({"fr": …, "en": …}) :
                            // sans cette ligne, le menu déroulant afficherait le JSON brut.
                            // (On renonce volontairement à ->searchable() ici : la recherche irait
                            // chercher dans la colonne JSON. Les domaines sont peu nombreux.)
                            ->getOptionLabelFromRecordUsing(fn (Domain $record): string => $record->getTranslation('name', 'fr'))
                            ->preload()
                            ->required(),

                        TextInput::make('slug')
                            ->label('Identifiant URL (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Adresse de la fiche sur le site public (ex. : ponts-kasandjala-i-et-ii).'),
                    ]),

                // 3. Où et quand le projet a-t-il eu lieu ?
                Section::make('Localisation & période')
                    ->columns(2)
                    ->schema([
                        TextInput::make('country')
                            ->label('Pays')
                            ->maxLength(255)
                            ->placeholder('RD Congo'),

                        TextInput::make('city')
                            ->label('Lieu précis (ville, territoire)')
                            ->maxLength(255)
                            ->placeholder('Sebele, Fizi'),

                        DatePicker::make('start_date')
                            ->label('Date de début')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('end_date')
                            ->label('Date de fin')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            // Règle métier : la fin d'un projet ne peut pas précéder son début.
                            ->afterOrEqual('start_date'),
                    ]),

                // 4. Combien a coûté le projet, et pour qui ?
                Section::make('Budget & bénéficiaires')
                    ->columns(2)
                    ->schema([
                        TextInput::make('budget_amount')
                            ->label('Montant du budget')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Chiffres uniquement, sans espace (ex. : 104918).'),

                        Select::make('budget_currency')
                            ->label('Devise')
                            ->options([
                                'USD' => 'USD — dollar américain',
                                'CDF' => 'CDF — franc congolais',
                                'EUR' => 'EUR — euro',
                            ])
                            ->default('USD'),

                        TextInput::make('beneficiaries_count')
                            ->label('Nombre de bénéficiaires')
                            ->numeric()
                            ->integer()
                            ->minValue(0),

                        TextInput::make('beneficiaries_unit')
                            ->label('Unité')
                            ->datalist(['personnes', 'habitants', 'ménages', 'élèves', 'femmes', 'filles', 'agriculteurs'])
                            ->helperText('Saisissez librement ou choisissez une valeur proposée.'),
                    ]),

                // 5. Ce que le public verra, et quand.
                Section::make('Publication & statut')
                    ->description('Ces réglages décident de ce qui est visible sur le site public.')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('État d\'avancement')
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::Planned)
                            ->required(),

                        Toggle::make('is_featured')
                            ->label('Mettre en avant sur l\'accueil')
                            ->helperText('Les projets mis en avant alimentent le bloc « Réalisations » de la page d\'accueil.'),

                        DateTimePicker::make('published_at')
                            ->label('Date de publication')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->columnSpanFull()
                            ->helperText('Laissez vide tant que la fiche n\'est pas prête : le site public n\'affiche que les projets datés et passés.'),
                    ]),

                // 6. Les partenaires du projet (relation N vers N).
                Section::make('Partenaires')
                    ->description('Bailleurs de fonds et partenaires d\'exécution associés à ce projet.')
                    ->schema([
                        Select::make('partners')
                            ->label('Partenaires')
                            ->relationship('partners', 'name')
                            ->multiple()
                            // Ici la recherche est fiable : la colonne "name" des partenaires
                            // est une vraie colonne texte (et non du JSON).
                            ->searchable()
                            ->preload()
                            ->helperText('Un ou plusieurs partenaires. Leur rôle (bailleur / exécutant) se précise depuis la fiche du partenaire.'),
                    ]),

                // 7. Les photos : une de couverture, puis la galerie.
                Section::make('Photos & illustrations')
                    ->description('Ces images alimentent la fiche du projet sur le site public. Conseils : JPG ou PNG, 1600 px de large minimum, moins de 5 Mo par image.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Photo de couverture')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans Project::registerMediaCollections().
                            ->collection('cover')
                            ->image()
                            ->maxSize(5120)
                            ->helperText('L\'image principale de la fiche : une seule photo possible (un nouvel envoi remplace l\'ancienne).'),

                        SpatieMediaLibraryFileUpload::make('photos')
                            ->label('Galerie photos')
                            ->collection('photos')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(24)
                            ->maxSize(5120)
                            ->helperText('Toutes les autres photos du projet. Glissez-déposez les vignettes pour choisir l\'ordre d\'affichage.'),
                    ]),
            ]);
    }
}
