<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['domain_id', 'title', 'slug', 'status', 'country', 'city', 'start_date', 'end_date', 'objectives', 'beneficiaries', 'results', 'contexte', 'objectif_general', 'composantes', 'credit_photo', 'budget_amount', 'budget_currency', 'beneficiaries_count', 'beneficiaries_unit', 'is_featured', 'published_at'])]
#[Translatable('title', 'objectives', 'beneficiaries', 'results', 'contexte', 'objectif_general', 'credit_photo')]
class Project extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'budget_amount' => 'decimal:2',       // ← sort toujours avec 2 décimales
            'beneficiaries_count' => 'integer',    // ← force le type entier
            'composantes' => 'array',               // ← liste d'objets {nom_fr, nom_en, …}
            'status' => ProjectStatus::class,      // ← 'ongoing' devient une valeur de l'enum
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->useDisk('public')
            ->storeConversionsOnDisk('public');
        $this->addMediaCollection('photos')
            ->useDisk('public')
            ->storeConversionsOnDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)->height(150)->nonQueued();

        $this->addMediaConversion('card')
            ->width(600)->height(400)->nonQueued();

        $this->addMediaConversion('hero')
            ->width(1200)->height(630)->nonQueued();
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Les partenaires de ce projet (relation N vers N).
     *
     * Le rôle (bailleur / exécutant) vit dans la table pivot : voir la classe
     * ProjectPartner, qui le convertit en enum PartnerRole.
     */
    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'project_partner')
            ->using(ProjectPartner::class)
            ->withTimestamps()
            ->withPivot('role');
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * La fiche possède-t-elle un contenu éditorial (« Contexte », « Objectif
     * général », « Composantes ») saisissable depuis l'admin ?
     *
     * Remplace l'ancien test du slug de la maquette : tout projet peut
     * afficher ces sections dès que du contenu existe, quelle que soit
     * la langue consultée.
     */
    public function hasEditorialContent(): bool
    {
        foreach (['fr', 'en'] as $lang) {
            if (filled($this->getTranslation('contexte', $lang))
                || filled($this->getTranslation('objectif_general', $lang))) {
                return true;
            }
        }

        return filled($this->composantes);
    }

    /**
     * Durée du projet en années + mois, calculée entre start_date et end_date.
     *
     * Retourne null quand la durée n'est pas calculable (pas de date de début,
     * ou projet toujours en cours sans date de fin).
     *
     * @return array{years: int, months: int}|null
     */
    public function durationParts(): ?array
    {
        if (! $this->start_date || ! $this->end_date) {
            return null;
        }

        $diff = $this->start_date->diff($this->end_date);

        // On arrondit à la quinzaine pour rester fidèle aux durées annoncées
        // dans la présentation OAAT (ex. 1 janv. → 30 juin = « 6 mois »).
        $totalMonths = ($diff->y * 12) + $diff->m + ($diff->d > 15 ? 1 : 0);

        return [
            'years'  => intdiv($totalMonths, 12),
            'months' => $totalMonths % 12,
        ];
    }

    /**
     * Libellé localisé de la durée du projet (ex. « 1 an 4 mois »).
     */
    public function durationLabel(?string $locale = null): string
    {
        $parts = $this->durationParts();

        if ($parts === null) {
            return match ($locale ?? app()->getLocale()) {
                'en' => 'To be determined',
                default => 'À déterminer',
            };
        }

        [$years, $months] = [$parts['years'], $parts['months']];
        $lang = $locale ?? app()->getLocale();

        if ($years === 0) {
            // Deux dates renseignées mais écart nul (ex. début = fin) :
            // « 0 mois » n'est pas un projet en cours, c'est une donnée vide.
            if ($months === 0) {
                return '—';
            }

            return $lang === 'en'
                ? ($months > 1 ? "{$months} months" : '1 month')
                : ($months > 1 ? "{$months} mois" : '1 mois');
        }

        if ($months === 0) {
            return $lang === 'en'
                ? ($years > 1 ? "{$years} years" : '1 year')
                : ($years > 1 ? "{$years} ans" : '1 an');
        }

        $yearLabel = $lang === 'en' ? ($years > 1 ? "{$years} years" : '1 year') : ($years > 1 ? "{$years} ans" : '1 an');
        $monthLabel = $lang === 'en' ? ($months > 1 ? "{$months} months" : '1 month') : ($months > 1 ? "{$months} mois" : '1 mois');

        return "{$yearLabel} {$monthLabel}";
    }

    /**
     * Budget formaté selon la langue (ex. « 541 591 USD », « 450 000 000 € »).
     */
    public function budgetLabel(?string $locale = null): string
    {
        if ($this->budget_amount === null) {
            return '—';
        }

        $amount = (float) $this->budget_amount;
        $formatted = ($locale ?? app()->getLocale()) === 'en'
            ? number_format($amount, 0, '.', ',')
            : number_format($amount, 0, ',', ' ');

        $currency = strtoupper((string) ($this->budget_currency ?? 'USD'));

        if ($currency === 'EUR') {
            return "{$formatted} €";
        }

        return "{$formatted} {$currency}";
    }

    /**
     * Année d'affichage du projet (année de début, sinon année de fin).
     * Utilisée pour le badge d'année des cartes « réalisés » de la maquette.
     */
    public function year(): ?int
    {
        return ($this->start_date ?? $this->end_date)?->year;
    }

    /**
     * Bénéficiaires formatés pour la fiche (nombre + unité, sinon texte traduit).
     *
     * Règle de la maquette : on privilégie la valeur chiffrée (beneficiaries_count
     * + unité) ; à défaut, le texte traduit (beneficiaries). S'il n'y a rien,
     * on renvoie un tiret « — » (les 6 cases sont toujours affichées).
     */
    public function beneficiariesLabel(?string $locale = null): string
    {
        if ($this->beneficiaries_count !== null) {
            // Format du nombre selon la langue : 1 234 en français, 1,234 en anglais.
            $count = ($locale ?? app()->getLocale()) === 'en'
                ? number_format($this->beneficiaries_count, 0, '.', ',')
                : number_format($this->beneficiaries_count, 0, ',', ' ');

            $unit = trim((string) $this->beneficiaries_unit);

            return $unit !== '' ? "{$count} {$unit}" : $count;
        }

        $text = trim((string) $this->getTranslation('beneficiaries', $locale ?? app()->getLocale()));

        return $text !== '' ? $text : '—';
    }

    /**
     * Noms des partenaires du projet (relation N-N), séparés par des virgules.
     * Renvoie « — » quand aucun partenaire n'est lié.
     */
    public function partnersLabel(?string $locale = null): string
    {
        if ($this->relationLoaded('partners') && $this->partners->isNotEmpty()) {
            return $this->partners->map(fn ($p) => trim((string) $p->name))
                ->filter()
                ->implode(', ');
        }

        return '—';
    }

    /**
     * Noms des partenaires chargés de la MISE EN ŒUVRE (rôle « implementer »
     * dans la table pivot project_partner). Renvoie « — » quand aucun.
     */
    public function implementationPartnersLabel(?string $locale = null): string
    {
        if ($this->relationLoaded('partners')) {
            $impl = $this->partners->filter(fn ($p) => ($p->pivot?->role?->value ?? null) === 'implementer');

            if ($impl->isNotEmpty()) {
                return $impl->map(fn ($p) => trim((string) $p->name))->filter()->implode(', ');
            }
        }

        return '—';
    }

    /**
     * Noms des bailleurs de fonds du projet (rôle « funder » dans la table pivot).
     * Renvoie « — » quand aucun bailleur n'est lié.
     */
    public function funderLabel(?string $locale = null): string
    {
        if ($this->relationLoaded('partners')) {
            $funders = $this->partners->filter(fn ($p) => ($p->pivot?->role?->value ?? null) === 'funder');

            if ($funders->isNotEmpty()) {
                return $funders->map(fn ($p) => trim((string) $p->name))->filter()->implode(', ');
            }
        }

        return '—';
    }

    /**
     * Libellé traduit du statut du projet (fiche projet, bloc « Autres projets »).
     *
     * L'idée : une seule source de vérité pour les libellés côté vue publique,
     * au lieu de multiplier les « match ($projet->status) » dans chaque template.
     */
    public function statusLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $fr = match ($this->status) {
            ProjectStatus::Completed => 'Réalisé',
            ProjectStatus::Ongoing => 'En cours',
            ProjectStatus::Planned => 'Planifié',
            ProjectStatus::AwaitingFunding => 'Recherche de financement',
            default => '—',
        };

        $en = match ($this->status) {
            ProjectStatus::Completed => 'Completed',
            ProjectStatus::Ongoing => 'Ongoing',
            ProjectStatus::Planned => 'Planned',
            ProjectStatus::AwaitingFunding => 'Seeking funding',
            default => '—',
        };

        return $locale === 'en' ? $en : $fr;
    }

    /**
     * Code court du statut pour la grille de la maquette (data-st).
     *
     * La maquette ne connaît que deux familles visuelles :
     *   - d → réalisé / en cours de réalisation (badge année, liseré moss) ;
     *   - w → en attente de financement (badge « À financer », liseré ochre).
     * planned et awaiting_funding rejoignent la famille w.
     */
    public function getStatutCourtAttribute(): string
    {
        return match ($this->status) {
            ProjectStatus::Completed, ProjectStatus::Ongoing => 'd',
            default => 'w',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
