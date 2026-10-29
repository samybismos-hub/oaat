<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Models\Domain;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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

                                Textarea::make('contexte.fr')
                                    ->label('Contexte et enjeux (FR)')
                                    ->rows(5)
                                    ->helperText('Séparez les paragraphes par une ligne vide. Éditées dans la section « Contexte et enjeux » de la fiche.'),

                                Textarea::make('objectif_general.fr')
                                    ->label('Objectif général (FR)')
                                    ->rows(3)
                                    ->helperText('La phrase qui résume l\'ambition du projet.'),
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

                                Textarea::make('contexte.en')
                                    ->label('Context and challenges (EN)')
                                    ->rows(5),

                                Textarea::make('objectif_general.en')
                                    ->label('General objective (EN)')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 1b. Contenu éditorial optionnel de la fiche : les sections
                //     « Contexte », « Objectif général » et le tableau des
                //     « Composantes » de la page publique (pages.projets-show).
                //     Laissées vides, ces sections n'apparaissent pas sur le site.
                Section::make('Contenu éditorial (optionnel)')
                    ->description('À renseigner seulement si la fiche doit détailler le contexte du projet et ses composantes (ex. projet emblématique).')
                    ->schema([
                        Repeater::make('composantes')
                            ->label('Composantes (tableau « Objectifs et composantes »)')
                            ->schema([
                                TextInput::make('nom_fr')
                                    ->label('Nom (FR)')
                                    ->required()
                                    ->maxLength(120),

                                TextInput::make('nom_en')
                                    ->label('Nom (EN)')
                                    ->maxLength(120),

                                Textarea::make('prevoit_fr')
                                    ->label('Ce que prévoit le projet (FR)')
                                    ->rows(2),

                                Textarea::make('prevoit_en')
                                    ->label('Planned works (EN)')
                                    ->rows(2),

                                Textarea::make('resultat_fr')
                                    ->label('Résultat attendu (FR)')
                                    ->rows(2),

                                Textarea::make('resultat_en')
                                    ->label('Expected result (EN)')
                                    ->rows(2),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une composante')
                            ->helperText('Triées par glisser-déposer : la première ligne apparaît en tête de tableau sur le site public.'),
                    ]),

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

                // 6. Les partenaires du projet ne se gèrent plus ici, mais dans l'onglet
                //    « Partenaires & rôles » de la fiche (gestionnaire de relations).
                //    Raison : chaque partenaire porte un rôle (bailleur / exécutant) qui
                //    vit dans la table pivot et qu'un simple menu déroulant multiple ne
                //    peut pas saisir. Un seul endroit gère donc la liste, ce qui évite
                //    qu'un enregistrement du formulaire, resté sur une liste périmée,
                //    ne détache un partenaire ajouté entre-temps depuis l'onglet.

                // 7. Les photos : la couverture ici ; la galerie se gère dans
                //    l'onglet « Albums photo » de la fiche. Un seul canal pour
                //    les photos, partagé avec la ressource « Albums photo » du
                //    menu « Médias & documents ».
                Section::make('Photos & illustrations')
                    ->description('Couverture et mention de crédit de la fiche. La galerie de photos se gère dans l\'onglet « Albums photo » de la fiche. Conseils images : JPG ou PNG, 1600 px de large minimum, moins de 5 Mo par image.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Photo de couverture')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans Project::registerMediaCollections().
                            ->collection('cover')
                            ->disk('public')
                            ->image()
                            ->maxSize(5120)
                            ->helperText('L\'image principale de la fiche : une seule photo possible (un nouvel envoi remplace l\'ancienne).'),

                        Placeholder::make('galerie')
                            ->label('Galerie photo')
                            ->columnSpanFull()
                            ->content('Téléversez vos photos dans l\'onglet « Albums photo » en bas de la fiche : elles apparaissent alors dans la section Galerie de la fiche publique. Chaque album regroupe ses photos sous un titre, et l\'ordre des photos se règle par glisser-déposer. Les albums généraux du site se gèrent dans le menu « Albums photo ».'),

                        Section::make('Crédit photo')
                            ->description('Mention affichée sous la photo de couverture et sous chaque photo de la galerie de la fiche. Laissez vide pour afficher « OAAT ».')
                            ->columns(2)
                            ->schema([
                                TextInput::make('credit_photo.fr')
                                    ->label('Crédit photo (FR)')
                                    ->placeholder('ex. : OAAT'),
                                TextInput::make('credit_photo.en')
                                    ->label('Crédit photo (EN)')
                                    ->placeholder('e.g. OAAT'),
                            ]),
                    ]),
            ]);
    }
}
