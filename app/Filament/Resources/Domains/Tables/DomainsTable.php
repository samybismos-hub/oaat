<?php

namespace App\Filament\Resources\Domains\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DomainsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position')
                    ->label('N°')
                    ->sortable(),

                TextColumn::make('icon')
                    ->label('Icône')
                    ->badge()
                    ->color('gray'),

                // La notation "name.fr" ne fonctionne pas : le modèle (Spatie HasTranslations)
                // renvoie déjà une chaîne pour la langue active. On lit donc chaque langue
                // explicitement avec getTranslation().
                TextColumn::make('name_fr')
                    ->label('Nom (FR)')
                    ->state(fn ($record) => $record->getTranslation('name', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('name->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('name->fr', $direction)),

                TextColumn::make('name_en')
                    ->label('Nom (EN)')
                    ->state(fn ($record) => $record->getTranslation('name', 'en'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('name->en', 'like', "%{$search}%"))
                    ->color('gray'),

                TextColumn::make('slug')
                    ->label('Identifiant URL (Slug)')
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position', 'asc')
            ->filters([
                //
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
