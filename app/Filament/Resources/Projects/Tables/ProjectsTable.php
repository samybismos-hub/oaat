<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Models\Domain;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Vignette provenant de la collection "cover" (gérée par spatie/laravel-medialibrary).
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Photo')
                    ->collection('cover')
                    ->height(40)
                    ->width(64),

                // Rappel : « title.fr » ne fonctionne pas avec Spatie (le modèle renvoie
                // une chaîne pour la langue active) ; on lit la traduction explicitement.
                TextColumn::make('title_fr')
                    ->label('Titre (FR)')
                    ->state(fn (Project $record): string => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('title->fr', $direction))
                    ->weight('bold')
                    ->limit(70),

                TextColumn::make('domain_fr')
                    ->label('Domaine')
                    ->state(fn (Project $record): ?string => $record->domain?->getTranslation('name', 'fr'))
                    ->badge()
                    ->color('gray'),

                // Grâce à l'enum ProjectStatus (contrats HasLabel + HasColor),
                // le libellé et la couleur de la pastille sont automatiques.
                TextColumn::make('status')
                    ->label('État')
                    ->badge()
                    ->sortable(),

                TextColumn::make('city')
                    ->label('Lieu')
                    ->state(fn (Project $record): ?string => collect([$record->city, $record->country])
                        ->filter()
                        ->implode(', '))
                    ->toggleable(),

                TextColumn::make('period')
                    ->label('Période')
                    ->state(function (Project $record): ?string {
                        if (blank($record->start_date)) {
                            return null;
                        }

                        $start = $record->start_date->format('m/Y');
                        $end = $record->end_date?->format('m/Y');

                        return $end ? "{$start} → {$end}" : "depuis {$start}";
                    })
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('start_date', $direction)),

                TextColumn::make('budget')
                    ->label('Budget')
                    ->state(fn (Project $record): ?string => blank($record->budget_amount)
                        ? null
                        : number_format((float) $record->budget_amount, 0, ',', ' ').' '.$record->budget_currency)
                    ->alignEnd()
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('budget_amount', $direction)),

                TextColumn::make('beneficiaries_count')
                    ->label('Bénéficiaires')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_featured')
                    ->label('À la une')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y')
                    ->placeholder('Brouillon')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('État d\'avancement')
                    ->options(ProjectStatus::class),

                SelectFilter::make('domain_id')
                    ->label('Domaine')
                    // Options fournies à la main : la colonne "name" des domaines est du JSON,
                    // une recherche SQL classique y donnerait des résultats trompeurs.
                    ->options(fn (): array => Domain::query()
                        ->orderBy('position')
                        ->get()
                        ->mapWithKeys(fn (Domain $domain): array => [$domain->id => $domain->getTranslation('name', 'fr')])
                        ->all()),

                TernaryFilter::make('is_featured')
                    ->label('Mise en avant')
                    ->placeholder('Tous les projets')
                    ->trueLabel('À la une')
                    ->falseLabel('Projets ordinaires'),

                TernaryFilter::make('published_at')
                    ->label('Publication')
                    ->nullable()
                    ->placeholder('Publiés et brouillons')
                    ->trueLabel('Publiés')
                    ->falseLabel('Brouillons (sans date)'),
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
