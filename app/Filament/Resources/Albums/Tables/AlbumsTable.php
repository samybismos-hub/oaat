<?php

namespace App\Filament\Resources\Albums\Tables;

use App\Models\Album;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlbumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // stacked() + limit(3) : trois vignettes empilées, puis « +5 ».
                SpatieMediaLibraryImageColumn::make('photos')
                    ->label('Aperçu')
                    ->collection('photos')
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->height(40)
                    ->width(64),

                TextColumn::make('title_fr')
                    ->label('Album (FR)')
                    ->state(fn (Album $record): string => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('title->fr', $direction))
                    ->weight('bold')
                    ->limit(60),

                TextColumn::make('project_fr')
                    ->label('Projet rattaché')
                    ->state(fn (Album $record): ?string => $record->project?->getTranslation('title', 'fr'))
                    ->placeholder('Album général')
                    ->badge()
                    ->color('gray'),

                // Compte les photos de l'album par une sous-requête COUNT :
                // une seule requête, pas une par ligne affichée.
                TextColumn::make('media_count')
                    ->label('Photos')
                    ->counts('media')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'gray' : 'warning')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                TernaryFilter::make('project_id')
                    ->label('Rattachement')
                    ->nullable()
                    ->placeholder('Tous les albums')
                    ->trueLabel('Albums rattachés à un projet')
                    ->falseLabel('Albums généraux'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
