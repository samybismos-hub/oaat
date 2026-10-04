<?php

namespace App\Filament\Resources\Actualities\Tables;

use App\Models\Actuality;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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

                IconColumn::make('is_featured')
                    ->label('À la une')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                // Un filtre à trois états. Le TernaryFilter des autres listes ne
                // suffirait pas ici : il ne distingue que « daté » et « non daté »,
                // alors que « programmée » et « en ligne » sont deux situations
                // différentes pour le client (l'une n'est pas encore visible).
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
            ])
            ->recordActions([
                EditAction::make(),
                // Pas de bouton « Télécharger » ici, contrairement aux documents :
                // une actualité est un article, pas un fichier à récupérer.
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
