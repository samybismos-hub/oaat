<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Enums\PartnerRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->collection('logo')
                    ->height(36),

                // Ici, `searchable()` sans astuce : « name » est une vraie colonne
                // texte (varchar), et non un champ JSON traduisible comme les titres
                // de domaines ou de projets. La recherche SQL fonctionne donc tel quel.
                TextColumn::make('name')
                    ->label('Partenaire')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('description_fr')
                    ->label('Présentation (FR)')
                    ->state(fn ($record): ?string => $record->getTranslation('description', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('description->fr', 'like', "%{$search}%"))
                    ->limit(70)
                    ->tooltip(fn ($record): ?string => $record->getTranslation('description', 'fr'))
                    ->toggleable(),

                // counts() ajoute une sous-requête COUNT en une seule fois :
                // pas de requête supplémentaire par ligne (piège classique du N+1).
                TextColumn::make('projects_count')
                    ->label('Projets')
                    ->counts('projects')
                    ->badge()
                    ->alignCenter()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle tenu sur un projet')
                    ->options(PartnerRole::class)
                    // Ce filtre interroge la table pivot : le whereHas() crée une
                    // sous-requête « EXISTS (… JOIN project_partner …) », d'où le nom
                    // de table explicitement qualifié.
                    ->query(function (Builder $query, array $data): Builder {
                        return filled($data['value'])
                            ? $query->whereHas('projects', fn (Builder $projects) => $projects->where('project_partner.role', $data['value']))
                            : $query;
                    }),
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
