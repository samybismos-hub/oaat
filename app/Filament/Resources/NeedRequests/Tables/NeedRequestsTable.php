<?php

namespace App\Filament\Resources\NeedRequests\Tables;

use App\Models\NeedRequest;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NeedRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_name')
                    ->label('Demandeur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('organization')
                    ->label('Organisation')
                    ->searchable()
                    ->placeholder('À titre individuel')
                    ->color('gray'),

                TextColumn::make('need_type')
                    ->label('Nature du besoin')
                    ->badge()
                    ->placeholder('Non précisé')
                    ->color('gray'),

                TextColumn::make('location')
                    ->label('Lieu')
                    ->placeholder('Lieu non précisé'),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Besoin décrit')
                    ->limit(50)
                    ->tooltip(fn (NeedRequest $record): string => $record->description)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                IconColumn::make('read_at')
                    ->label('Lu')
                    ->state(fn (NeedRequest $record): bool => $record->read_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Les « natures de besoin » ne sont pas une liste fermée : elles
                // dépendent des choix offerts par le formulaire public. On
                // construit donc le filtre à partir des valeurs réellement reçues.
                SelectFilter::make('need_type')
                    ->label('Nature du besoin')
                    ->options(fn (): array => NeedRequest::query()
                        ->whereNotNull('need_type')
                        ->distinct()
                        ->orderBy('need_type')
                        ->pluck('need_type', 'need_type')
                        ->all()),

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
                ViewAction::make()
                    ->label('Lire')
                    ->modalHeading(fn (NeedRequest $record): string => 'Demande de '.$record->contact_name)
                    ->schema([
                        TextEntry::make('contact_name')->label('Demandeur'),
                        TextEntry::make('organization')->label('Organisation')->placeholder('À titre individuel'),
                        TextEntry::make('email')->label('Adresse e-mail'),
                        TextEntry::make('phone')->label('Téléphone')->placeholder('Non communiqué'),
                        TextEntry::make('need_type')->label('Nature du besoin')->placeholder('Non précisée'),
                        TextEntry::make('location')->label('Lieu')->placeholder('Lieu non précisé'),
                        TextEntry::make('created_at')->label('Reçu le')->dateTime('d/m/Y à H:i'),
                        TextEntry::make('read_at')->label('Lu le')->dateTime('d/m/Y à H:i')->placeholder('Pas encore lu'),
                        TextEntry::make('description')->label('Besoin décrit'),
                    ]),

                // Marquer la demande comme lue (bouton visible seulement si pas encore lue).
                Action::make('marquerLu')
                    ->label('Marquer comme lu')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->visible(fn (NeedRequest $record): bool => $record->read_at === null)
                    ->action(fn (NeedRequest $record): mixed => $record->update(['read_at' => now()])),

                Action::make('repondre')
                    ->label('Répondre')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->url(fn (NeedRequest $record): string => 'mailto:'.$record->email)
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
