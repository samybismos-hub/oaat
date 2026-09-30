<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Enums\DocumentType;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_fr')
                    ->label('Titre (FR)')
                    ->state(fn (Document $record): string => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('title->fr', $direction))
                    ->weight('bold')
                    ->limit(70),

                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->state(fn (Document $record): ?string => $record->getTranslation('title', 'en'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->en', 'like', "%{$search}%"))
                    ->color('gray')
                    ->limit(60)
                    ->toggleable(),

                // Grâce à l'enum DocumentType, la pastille se libelle et se colore seule.
                TextColumn::make('type')
                    ->label('Nature')
                    ->badge()
                    ->placeholder('Non classé')
                    ->sortable(),

                // Indicateur de complétude : un document sans fichier est une fiche
                // annoncée mais introuvable pour le visiteur. Autant le voir d'un
                // coup d'œil depuis la liste (les documents semés sont dans ce cas).
                TextColumn::make('fichier')
                    ->label('Fichier')
                    ->badge()
                    ->state(fn (Document $record): string => $record->hasMedia('file') ? 'En ligne' : 'Manquant')
                    ->color(fn (string $state): string => $state === 'En ligne' ? 'success' : 'warning')
                    ->icon(fn (string $state): Heroicon => $state === 'En ligne'
                        ? Heroicon::OutlinedDocumentArrowDown
                        : Heroicon::OutlinedExclamationTriangle),

                TextColumn::make('published_at')
                    ->label('En ligne depuis')
                    ->dateTime('d/m/Y')
                    ->placeholder('Brouillon')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Nature du document')
                    ->options(DocumentType::class),

                TernaryFilter::make('published_at')
                    ->label('Publication')
                    ->nullable()
                    ->placeholder('Publiés et brouillons')
                    ->trueLabel('En ligne')
                    ->falseLabel('Brouillons (sans date)'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('telecharger')
                    ->label('Télécharger')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    // getFirstMedia('file') renvoie le fichier de la collection
                    // « file » ; en l'absence de fichier, le bouton n'existe pas.
                    ->url(fn (Document $record): ?string => $record->hasMedia('file')
                        ? $record->getFirstMedia('file')->getUrl()
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Document $record): bool => $record->hasMedia('file')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
