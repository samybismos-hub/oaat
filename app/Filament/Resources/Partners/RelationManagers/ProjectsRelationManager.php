<?php

namespace App\Filament\Resources\Partners\RelationManagers;

use App\Enums\PartnerRole;
use App\Models\Project;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * L'onglet « Projets » d'une fiche partenaire.
 *
 * C'est ici que l'on répond à la question « ce partenaire a-t-il financé ou
 * exécuté tel projet ? » : la colonne Rôle est éditable directement dans le
 * tableau, et la modification est enregistrée dans la table pivot
 * `project_partner` (voir la classe App\Models\ProjectPartner).
 */
class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projets & rôle du partenaire';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Rappel : « title » est traduisible (JSON), on lit donc le français
                // explicitement, comme dans le tableau des projets.
                TextColumn::make('title_fr')
                    ->label('Projet (FR)')
                    ->state(fn (Project $record): string => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('title->fr', $direction))
                    ->weight('bold')
                    ->limit(60),

                // Colonne éditable : le rôle n'est pas une colonne du projet mais une
                // colonne de la table pivot. Filament le détecte tout seul, parce que
                // « role » figure dans le withPivot() de la relation Project::partners(),
                // et enregistre donc la modification dans project_partner.
                SelectColumn::make('role')
                    ->label('Rôle pour ce projet')
                    ->options(PartnerRole::class)
                    ->state(fn (Project $record): ?string => $record->pivot?->role?->value)
                    ->placeholder('À préciser'),

                TextColumn::make('status')
                    ->label('État')
                    ->badge(),

                TextColumn::make('city')
                    ->label('Lieu')
                    ->toggleable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle')
                    ->options(PartnerRole::class)
                    // Le tableau interroge déjà la table pivot (relation N vers N) :
                    // la colonne est donc qualifiée par le nom de cette table.
                    ->query(function (Builder $query, array $data): Builder {
                        return filled($data['value'])
                            ? $query->where('project_partner.role', $data['value'])
                            : $query;
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Associer un projet')
                    ->modalHeading('Associer un projet à ce partenaire')
                    // Le schéma du formulaire d'association : le sélecteur de projet
                    // fourni par Filament, plus notre champ « role ». Tout champ portant
                    // le nom d'une colonne pivot est transmis à attach() comme donnée
                    // de la table pivot.
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->label('Rôle de ce partenaire')
                            ->options(PartnerRole::class)
                            ->helperText('Laissez vide si le rôle n\'est pas encore documenté.'),
                    ]),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Retirer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
