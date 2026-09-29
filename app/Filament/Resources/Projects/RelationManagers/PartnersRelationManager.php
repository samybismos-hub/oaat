<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\PartnerRole;
use App\Models\Partner;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * L'onglet « Partenaires » d'une fiche projet : qui finance, qui exécute.
 *
 * Ce gestionnaire remplace l'ancien menu déroulant multiple du formulaire.
 * Un seul endroit gère donc la liste des partenaires d'un projet, et le rôle de
 * chacun est modifiable en ligne. C'est volontaire : si le formulaire principal
 * réenregistrait la liste des partenaires en même temps que ce tableau, un
 * partenaire tout juste associé ici risquerait d'être détaché par un
 * enregistrement du formulaire resté sur une liste périmée.
 */
class PartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';

    protected static ?string $title = 'Partenaires & rôles';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->collection('logo')
                    ->height(32),

                // « name » n'est pas traduisible : recherche et tri SQL ordinaires.
                TextColumn::make('name')
                    ->label('Partenaire')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                SelectColumn::make('role')
                    ->label('Rôle sur ce projet')
                    ->options(PartnerRole::class)
                    ->state(fn (Partner $record): ?string => $record->pivot?->role?->value)
                    ->placeholder('À préciser'),

                TextColumn::make('description_fr')
                    ->label('Présentation (FR)')
                    ->state(fn (Partner $record): ?string => $record->getTranslation('description', 'fr'))
                    ->limit(60)
                    ->toggleable(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle')
                    ->options(PartnerRole::class)
                    ->query(function (Builder $query, array $data): Builder {
                        return filled($data['value'])
                            ? $query->where('project_partner.role', $data['value'])
                            : $query;
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Associer un partenaire')
                    ->modalHeading('Associer un partenaire à ce projet')
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->label('Rôle de ce partenaire')
                            ->options(PartnerRole::class)
                            ->helperText('Bailleur de fonds, ou partenaire chargé de l\'exécution sur le terrain.'),
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
