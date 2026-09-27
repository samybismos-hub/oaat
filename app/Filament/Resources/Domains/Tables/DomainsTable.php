<?php

namespace App\Filament\Resources\Domains\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

                TextColumn::make('name.fr')
                    ->label('Nom (FR)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.en')
                    ->label('Nom (EN)')
                    ->searchable(),

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
