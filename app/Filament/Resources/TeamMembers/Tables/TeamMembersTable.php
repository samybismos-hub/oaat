<?php

namespace App\Filament\Resources\TeamMembers\Tables;

use App\Models\TeamMember;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->label('Portrait')
                    ->collection('photo')
                    ->circular()
                    ->height(40)
                    ->width(40),

                TextColumn::make('position')
                    ->label('N°')
                    ->sortable(),

                // « name » n'est pas traduisible : recherche et tri SQL ordinaires.
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('role_fr')
                    ->label('Fonction (FR)')
                    ->state(fn (TeamMember $record): ?string => $record->getTranslation('role', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('role->fr', 'like', "%{$search}%")),

                TextColumn::make('role_en')
                    ->label('Position (EN)')
                    ->state(fn (TeamMember $record): ?string => $record->getTranslation('role', 'en'))
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position', 'asc')
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
