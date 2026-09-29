<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // La notation "title.fr" ne fonctionne pas : le modèle (Spatie HasTranslations)
                // renvoie déjà une chaîne pour la langue active. On lit donc chaque langue
                // explicitement avec getTranslation().
                TextColumn::make('title_fr')
                    ->label('Titre (FR)')
                    ->state(fn ($record) => $record->getTranslation('title', 'fr'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->weight('bold'),

                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->state(fn ($record) => $record->getTranslation('title', 'en'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->where('title->en', 'like', "%{$search}%"))
                    ->color('gray'),

                TextColumn::make('slug')
                    ->label('Identifiant système')
                    ->badge()
                    ->color('info'),

                // Indicateur de référencement : signale d'un coup d'œil les pages
                // dont la description SEO (celle affichée par Google) manque.
                TextColumn::make('seo_fr')
                    ->label('SEO (FR)')
                    ->badge()
                    ->state(fn (Page $record): string => filled($record->getTranslations('meta_description')['fr'] ?? null)
                        ? 'Renseigné'
                        : 'À compléter')
                    ->color(fn (string $state): string => $state === 'Renseigné' ? 'success' : 'warning'),

                TextColumn::make('updated_at')
                    ->label('Dernière modification')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
