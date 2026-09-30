<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Enums\DocumentType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Le titre. Un seul champ est traduisible ici : deux onglets
                //    FR/EN pour une seule case à remplir seraient plus lourds
                //    qu'utiles. On place donc les deux langues côte à côte.
                Section::make('Titre du document')
                    ->description('Ce titre apparaît dans la liste des documents téléchargeables sur le site public.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title.fr')
                            ->label('Titre (FR)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ex. : Présentation de l\'OAAT et de ses expériences avec les partenaires.'),

                        TextInput::make('title.en')
                            ->label('Title (EN)')
                            ->maxLength(255)
                            ->helperText('Laisser vide si le document n\'existe qu\'en français.'),
                    ]),

                // 2. Le fichier lui-même, et son classement.
                Section::make('Fichier & classement')
                    ->description('Le fichier téléversé remplace le précédent : il n\'y a jamais deux versions en ligne.')
                    ->columns(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('file')
                            ->label('Fichier à télécharger')
                            // Le nom de la collection doit correspondre exactement à celui
                            // déclaré dans Document::registerMediaCollections().
                            ->collection('file')
                            ->disk('public')
                            // Pas de ->image() ici : un document est presque toujours un PDF.
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->maxSize(20480)
                            ->helperText('PDF, Word ou Excel, 20 Mo maximum. Un nouvel envoi remplace le fichier précédent.')
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Nature du document')
                            ->options(DocumentType::class)
                            ->native(false)
                            ->helperText('Sert à regrouper les documents par famille sur le site public.'),

                        DateTimePicker::make('published_at')
                            ->label('Date de mise en ligne')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Laisser vide tant que le document n\'est pas validé : le site public n\'affiche que les documents datés et passés.'),
                    ]),
            ]);
    }
}
