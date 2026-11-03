<?php

namespace App\Http\Controllers\Public;

use App\Models\Domain;
use App\Models\Page;
use App\Models\Project;
use App\Models\Setting;

class DomaineController
{
    public function index(string $locale): mixed
    {
        $domaines = Domain::query()
            ->with('projects', fn ($q) => $q->published()->with('partners'))
            ->withCount('projects')
            ->orderBy('position')
            ->get();

        $projetsCount = Project::query()
            ->published()
            ->count();
        $zonesCount = (int) (Setting::query()->first()?->stat_zones ?? 0);

        // Page CMS « domaines » : SEO, héro, compétences transversales et bandeau
        // d'appel à l'action sont éditables dans Filament → Pages du site.
        $pageDomaines = Page::query()->where('slug', 'domaines')->first();

        // ─── S1 — Toute la préparation d'affichage se fait ici, plus dans la vue :
        // une structure prête à afficher par domaine (filtres, période, entités
        // partenaires dédupliquées par id, lignes du bloc « Projets associés »).
        $sections = $domaines->map(function (Domain $d) use ($locale) {
            $projets = $d->projects; // publiés + partenaires (chargés ci-dessus)

            $realises = $projets->filter(fn ($p) => $p->statut_court === 'd')
                ->sortByDesc(fn ($p) => $p->year() ?? 0)
                ->values();
            $aFinancer = $projets->filter(fn ($p) => $p->statut_court === 'w')->values();

            $annees = $realises->map(fn ($p) => $p->year())->filter()->values();

            // ─── S2 — Entités partenaires dédupliquées par ID (jamais de split de
            // chaîne) et filtrées sur le rôle du pivot `project_partner`. Un même
            // partenaire peut légitimement être dans les deux listes.
            $bailleurs = $projets
                ->flatMap(fn ($p) => $p->partners->filter(fn ($part) => ($part->pivot?->role?->value ?? null) === 'funder'))
                ->unique('id')
                ->values();
            $implementeurs = $projets
                ->flatMap(fn ($p) => $p->partners->filter(fn ($part) => ($part->pivot?->role?->value ?? null) === 'implementer'))
                ->unique('id')
                ->values();

            $bailleursLabel = $bailleurs->pluck('name')->filter()->unique()->values()->implode(', ');
            $implementeursLabel = $implementeurs->pluck('name')->filter()->unique()->values()->implode(', ');

            // ─── S3 — Lignes compactes du bloc « Projets associés », lien vers la
            // fiche projet (projets.show) inclus.
            $parcours = $realises->concat($aFinancer)->map(fn (Project $p) => (object) [
                'url'         => route('projets.show', ['locale' => $locale, 'slug' => $p->slug]),
                'titre'       => (string) ($p->getTranslation('title', $locale) ?: $p->getTranslation('title', 'fr')),
                'annee'       => $p->year() ?? '',
                'afinancer'   => $p->statut_court === 'w',
                'funderLabel' => $p->funderLabel($locale),
                'implLabel'   => $p->implementationPartnersLabel($locale),
            ])->values();

            return (object) [
                'domaine'            => $d,
                'nom'                => $d->getTranslation('name', $locale) ?: $d->slug,
                'intro'              => $d->getTranslation('intro', $locale)?: '',
                'objectives'         => $d->getTranslation('objectives', $locale)?: '',
                'interventions'      => (array) $d->getTranslation('interventions', $locale) ?? [],
                'formations'         => (array) $d->getTranslation('formations', $locale) ?? [],
                'realises'           => $realises,
                'aFinancer'          => $aFinancer,
                'periode'            => $annees->isEmpty() ? '' : $annees->min().'–'.$annees->max(),
                'bailleurs'          => $bailleurs,
                'implementeurs'      => $implementeurs,
                'bailleursLabel'     => $bailleursLabel,
                'implementeursLabel' => $implementeursLabel,
                'parcours'           => $parcours,
            ];
        })->values();
            // Libellé court du rail pour un domaine (fallback = nom traduit, puis slug).
        $railLabel = static function ($d) use ($locale) {
            $label = config('oaat.domaine_page.rail.'.$d->slug);

            return $label ?: (string) ($d->getTranslation('name', $locale) ?: $d->slug);
        };

        // Pictogramme SVG (design figé dans config), chaîne vide si jamais absent.
        $icone = static fn ($d) => (string) (config('oaat.domaine_page.icons.'.$d->slug) ?? '');

        // Teinte HSL (design figé), défaut 200 si jamais absent.
        $teinte = static fn ($d) => (int) (config('oaat.domaine_page.tints.'.$d->slug) ?? 200);
        // Compétences transversales (page CMS), repli français géré par le modèle.
        $competences = $pageDomaines ? $pageDomaines->competencesFor($locale) : [];

        $compteurs = [
            'domaines' => $domaines->count(),
            'projets'  => (int) $projetsCount,
            'zones'    => (int) $zonesCount,
        ];

        return view('pages.domaines-index', [
            'domaines'     => $domaines,
            'sections'     => $sections,
            'competences'  => $competences,
            'projetsCount' => $projetsCount,
            'zonesCount'   => $zonesCount,
            'pageDomaines' => $pageDomaines,
            'locale'       => $locale,
            'railLabel'    => $railLabel,
            'icone'    => $icone,
            'teinte'    => $teinte,
            'compteurs'    => $compteurs,
        ]);
    }


    public function show(string $locale, string $slug): mixed
    {
        $domaine = Domain::query()
            ->where('slug', $slug)
            ->with('projects', fn ($q) => $q->published()->latest('published_at'))
            ->firstOrFail();

        return view('pages.domaines-show', [
            'domaine' => $domaine,
            'locale'  => $locale,
        ]);
    }
}
