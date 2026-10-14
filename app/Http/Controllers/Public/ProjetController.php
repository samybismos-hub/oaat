<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProjectStatus;
use App\Models\Domain;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

/**
 * ProjetController — Pages publiques « Projets ».
 *
 * Le rendu de la grille est SERVEUR (pas d'injection JS comme la maquette) :
 * les filtres de la barre latérale sont donc résolus ici grâce aux query
 * strings GET, et les données arrivées dans la vue sont prêtes à être
 * affichées telles quelles.
 */
class ProjetController
{
    /**
     * Les périodes d'intervention proposées dans le filtre « Période ».
     * Maquette : 4 plages entre 1994 et 2024.
     *
     * @return array<int, array{label: string, debut: int, fin: int}>
     */
    private function periodes(): array
    {
        return [
            ['label' => '1994–2000', 'debut' => 1994, 'fin' => 2000],
            ['label' => '2005–2010', 'debut' => 2005, 'fin' => 2010],
            ['label' => '2013–2019', 'debut' => 2013, 'fin' => 2019],
            ['label' => '2021–2024', 'debut' => 2021, 'fin' => 2024],
        ];
    }

    /**
     * Liste les projets publiés, filtrés par statut / domaine / recherche.
     *
     * @param  string  $locale   "fr" ou "en"
     */
    public function index(string $locale): mixed
    {

        // ─── 1. Filtres lus depuis les query strings GET ──────────────
        $statut = (string) request()->query('statut', 'all');
        $domaine = (string) request()->query('domaine', '');

        // La maquette utilise #q pour la recherche (« q »). On accepte
        // « recherche » en alias pour rester explicite dans l'URL.
        $recherche = trim((string) request()->query('q', request()->query('recherche', '')));

        $periode = (string) request()->query('periode', '');

        $statutsValides = ['all', 'completed', 'awaiting_funding'];
        if (! in_array($statut, $statutsValides, true)) {
            $statut = 'all';
        }

        // ─── 2. Requête Eloquent + relations (domain, partners) ───────
        $projets = Project::query()
            ->published()
            ->with(['domain', 'partners']);

        // 2a. Filtre par statut.
        if ($statut === 'completed') {
            $projets->where('status', ProjectStatus::Completed->value);
        } elseif ($statut === 'awaiting_funding') {
            $projets->where('status', ProjectStatus::AwaitingFunding->value);
        }
        // « all » (défaut) : tous les statuts, y compris ongoing et planned.

        // 2b. Filtre par domaine (slug).
        if ($domaine !== '') {
            $projets->whereHas('domain', fn (Builder $query) => $query->where('slug', $domaine));
        }

        // 2c. Recherche plein texte dans les champs traduits + le lieu.
        // La notation « title->fr » est compilée par le query builder en
        // JSON_UNQUOTE(JSON_EXTRACT(...)) sur MySQL et json_extract(...) sur
        // SQLite : elle est portable sur les deux moteurs.
        $q = trim(request()->input('q', ''));
        if ($recherche !== '') {
            $like = '%' . $recherche . '%';

            $projets->where(function (Builder $query) use ($like, $locale) {
                $query
                    ->where('title->' . $locale, 'like', $like)
                    ->orWhere('objectives->' . $locale, 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('country', 'like', $like);
            });
        }

        // 2d. Filtre par période (4 plages d'années).
        if (is_numeric($periode)) {
            $tranches = $this->periodes();
            $idx = (int) $periode;
            if (isset($tranches[$idx])) {
                $projets->whereYear('start_date', '>=', $tranches[$idx]['debut'])
                        ->whereYear('start_date', '<=', $tranches[$idx]['fin']);
            }
        }

        // Maquette : les projets « à financer » d'abord, puis les réalisés
        // les plus récents en premier.
        $projets = $projets
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END, start_date DESC', [ProjectStatus::AwaitingFunding->value])
            ->get();

        // ─── 3. Domaines pour la barre de filtres (compteurs réels) ───
        // Tri en PHP : trier par la colonne JSON name en SQL n'est pas portable
        // (ORDER BY sur JSON est refusé par MySQL). On trie par position puis
        // par nom traduit dans la langue active.
        $domaines = Domain::query()
            ->withCount(['projects' => fn (Builder $query) => $query->published()])
            ->whereHas('projects', fn (Builder $query) => $query->published())
            ->get()
            ->sortBy([
                ['position', 'asc'],
                [fn (Domain $domain) => mb_strtolower($domain->getTranslation('name', $locale)), 'asc'],
            ])
            ->values();

        // ─── 3b. Barres de progression pour les domaines ─────────────
        $maxProjets = $domaines->max('projects_count') ?: 1;
        foreach ($domaines as $d) {
            $d->barWidth = round(($d->projects_count / $maxProjets) * 100);
        }

        // ─── 4. Statuts pour la barre de filtres (compteurs réels) ────
        $compteurs = [
            'total'      => Project::query()->published()->count(),
            'realises'   => Project::query()->published()->where('status', ProjectStatus::Completed->value)->count(),
            'en_attente' => Project::query()->published()->where('status', ProjectStatus::AwaitingFunding->value)->count(),
            'en_cours'   => Project::query()->published()->where('status', ProjectStatus::Ongoing->value)->count(),
            'domaines'   => Domain::query()->whereHas('projects', fn (Builder $query) => $query->published())->count(),
            'zones'      => Project::query()->published()->distinct('city')->count('city'),
        ];

        $statuts = [
            ['cle' => 'all',              'count' => $compteurs['total']],
            ['cle' => 'completed',        'count' => $compteurs['realises']],
            ['cle' => 'awaiting_funding', 'count' => $compteurs['en_attente']],
        ];

        // ─── 4b. Page « projets » pour le sous-titre éditable ──────────
        $pageProjets = Page::query()->where('slug', 'projets')->first();

        // ─── 5. Rendu de la vue ───────────────────────────────────────
        return view('pages.projets-index', [
            'projets'    => $projets,
            'domaines'   => $domaines,
            'statuts'    => $statuts,
            'periodes'   => $this->periodes(),
            'compteurs'  => $compteurs,
            'statut'     => $statut,
            'domaine'    => $domaine,
            'recherche'  => $recherche,
            'periode'    => $periode,
            'pageProjets' => $pageProjets,
            'locale'     => $locale,
        ]);
    }

    public function show(string $locale, string $slug): mixed
    {
        $projet = Project::query()
            ->where('slug', $slug)
            ->with('domain')
            ->with('partners')
            ->with('albums')
            ->firstOrFail();

        return view('pages.projets-show', [
            'projet' => $projet,
            'locale' => $locale,
        ]);
    }
}
