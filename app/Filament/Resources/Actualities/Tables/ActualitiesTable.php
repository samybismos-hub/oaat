<?php

namespace App\Filament\Resources\Actualities\Tables;

use App\Enums\ActualityType;
use App\Models\Actuality;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
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

class ActualitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Couverture')
                    ->collection('cover')
                    ->height(40),

                TextColumn::make('title_fr')
                    ->label('Titre (FR)')
                    ->state(fn (Actuality $record): string => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('title->fr', $direction))
                    ->weight('bold')
                    ->limit(70),

                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->state(fn (Actuality $record): ?string => $record->getTranslation('title', 'en'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->en', 'like', "%{$search}%"))
                    ->color('gray')
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('etat')
                    ->label('État')
                    ->badge()
                    ->state(fn (Actuality $record): string => match (true) {
                        $record->published_at === null => 'Brouillon',
                        $record->published_at->isFuture() => 'Programmée',
                        default => 'En ligne',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'En ligne' => 'success',
                        'Programmée' => 'info',
                        default => 'warning',
                    }),

                TextColumn::make('published_at')
                    ->label('En ligne depuis')
                    ->dateTime('d/m/Y')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder("-")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('etat')
                    ->label('État')
                    ->options(['brouillon' => 'Brouillon', 'programmee' => 'Programmée', 'enligne' => 'En ligne'])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'brouillon' => $query->whereNull('published_at'),
                            'programmee' => $query->where('published_at', '>', now()),
                            'enligne' => $query->published(),
                            default => $query,
                        };
                    }),

                TernaryFilter::make('published_at')
                    ->label('Publication')
                    ->nullable()
                    ->placeholder('Publiées et brouillons')
                    ->trueLabel('En ligne et programmées')
                    ->falseLabel('Brouillons (sans date)'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('telecharger')
                    ->label('Télécharger')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    // getFirstMedia('file') renvoie le fichier de la collection
                    // « file » ; en l'absence de fichier, le bouton n'existe pas.
                    ->url(fn (Actuality $record): ?string => $record->hasMedia('file')
                        ? $record->getFirstMedia('file')->getUrl()
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Actuality $record): bool => $record->hasMedia('file')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
