<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Expéditeur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->color('gray')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->copyable(),

                // « subject » est facultatif : la colonne indique « Sans objet »
                // au lieu d'un vide ambigu.
                TextColumn::make('subject')
                    ->label('Objet')
                    ->searchable()
                    ->placeholder('Sans objet')
                    ->limit(50),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    // La bulle affiche le texte complet au survol : inutile
                    // d'ouvrir le message pour en connaître le contenu.
                    ->tooltip(fn (ContactMessage $record): string => $record->message)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                IconColumn::make('read_at')
                    ->label('Lu')
                    ->state(fn (ContactMessage $record): bool => $record->read_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filtre sur mesure : deux dates encadrant la réception.
                // Les noms « du » et « au » sont ceux des champs ci-dessous ;
                // ils arrivent dans la fermeture de query() via $data.
                Filter::make('recu')
                    ->label('Période de réception')
                    ->schema([
                        DatePicker::make('du')
                            ->label('Reçu à partir du')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('au')
                            ->label('Reçu jusqu\'au')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['du']), fn (Builder $q) => $q->whereDate('created_at', '>=', $data['du']))
                            ->when(filled($data['au']), fn (Builder $q) => $q->whereDate('created_at', '<=', $data['au']));
                    }),
            ])
            ->recordActions([
                // La lecture se fait dans une fenêtre : rien n'est modifiable,
                // les champs sont donc affichés en lecture seule par Filament.
                ViewAction::make()
                    ->label('Lire')
                    ->modalHeading(fn (ContactMessage $record): string => 'Message de '.$record->name)
                    ->schema([
                        TextEntry::make('name')->label('Expéditeur'),
                        TextEntry::make('email')->label('Adresse e-mail'),
                        TextEntry::make('subject')->label('Objet')->placeholder('Sans objet'),
                        TextEntry::make('created_at')->label('Reçu le')->dateTime('d/m/Y à H:i'),
                        TextEntry::make('read_at')->label('Lu le')->dateTime('d/m/Y à H:i')->placeholder('Pas encore lu'),
                        TextEntry::make('message')->label('Message'),
                    ]),

                // Marquer le message comme lu (bouton visible seulement s'il ne l'est pas).
                // Un clic met read_at à l'instant présent et rafraîchit la ligne.
                Action::make('marquerLu')
                    ->label('Marquer comme lu')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->visible(fn (ContactMessage $record): bool => $record->read_at === null)
                    ->action(fn (ContactMessage $record): mixed => $record->update(['read_at' => now()])),

                // Répondre ouvre le logiciel de messagerie de l'utilisateur,
                // avec l'adresse du visiteur et l'objet déjà remplis.
                Action::make('repondre')
                    ->label('Répondre')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->url(function (ContactMessage $record): string {
                        $sujet = filled($record->subject) ? '?subject='.rawurlencode('Re: '.$record->subject) : '';

                        return 'mailto:'.$record->email.$sujet;
                    })
                    ->openUrlInNewTab(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
