<?php

namespace App\Http\Controllers\Public;

use App\Models\Actuality;
use App\Models\Domain;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TeamMember;

/**
 * AccueilController — Page d'accueil du site public.
 *
 * Rassemble les données de 7 tables :
 * - Domaines d'intervention     (les 9)
 * - Projets à la une            (is_featured = true, jusqu'à 3)
 * - Projet phare                (statut fundraising prioritaire)
 * - Actualités récentes         (les 3 dernières publiées)
 * - Partenaires                 (tous, triés par nom, pour les logos)
 * - Chiffres clés               (Settings + comptages réels en base)
 * - Page d'accueil              (slug 'accueil', pour le hero et le SEO)
 */
class AccueilController
{
    /**
     * Affiche la page d'accueil.
     *
     * @param  string  $locale   "fr" ou "en"
     * @return \Illuminate\Http\Response
     */
    public function index(string $locale): mixed
    {
        // ─── 1. Paramètres généraux du site ────────────────────
        $settings = Setting::current();

        // ─── 2. Domaines d'intervention (les 9, triés par nom) ─
        $domaines = Domain::query()
            ->orderBy('name')
            ->get();

        // ─── 3. Projets à la une (jusqu'à 3, avec leur domaine) ─
        $projets = Project::query()
            ->where('is_featured', true)
            ->published()
            ->with('domain')
            ->latest('published_at')
            ->take(3)
            ->get();

        // ─── 4. Projet phare (bannière, priorité au statut fundraising) ─
        $projetPhare = Project::query()
            ->published()
            ->where('status', 'awaiting_funding')
            ->with('domain')
            ->latest('published_at')
            ->first();
        if (! $projetPhare) {
            $projetPhare = $projets->first();
        }

        // ─── 5. Compteurs pour la section "chiffres clés" (COUNT SQL) ─
        $projetsRealises = Project::query()
            ->published()
            ->where('status', 'completed')
            ->count();

        $projetsAF = Project::query()
            ->published()
            ->where('status', 'awaiting_funding')
            ->count();

        // ─── 6. Actualités récentes (3 dernières publiées) ────
        $actualites = Actuality::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        // ─── 7. Partenaires (tous, triés par nom) ─────────────
        $partenaires = Partner::query()
            ->orderBy('name')
            ->get();

        // ─── 8. Fondateur (membre marqué is_founder, pour _about) ─
        $founder = TeamMember::query()
            ->where('is_founder', true)
            ->first();

        // ─── 9. Années d'activité (calculée depuis la fondation) ─
        $yearsOfActivity = (int) now()->diffInYears(config('oaat.founded_at', '1995-05-10'), true);

        // ─── 10. Page d'accueil pour le hero et le SEO ────────
        $organisation = Page::query()
            ->where('slug', 'accueil')
            ->first();

        // ─── 11. Page Organisation pour la section Mission ─────
        $pageOrganisation = Page::query()
            ->where('slug', 'organisation')
            ->first();

        // ─── 11. Timeline et reconnaissances depuis les colonnes JSON ──────
        $timeline = [];
        $recognitions = [];
        $zonesCount = 0;

        if ($pageOrganisation) {
            // Timeline
            foreach ((array) ($pageOrganisation->getTranslation('timeline', $locale) ?? []) as $item) {
                if (is_array($item)) {
                    $timeline[] = [$item['year'] ?? '', $item['title'] ?? '', $item['description'] ?? ''];
                }
            }

            // Reconnaissances
            foreach ((array) ($pageOrganisation->getTranslation('recognitions', $locale) ?? []) as $item) {
                if (is_array($item)) {
                    $recognitions[] = [$item['title'] ?? '', $item['authority'] ?? '', $item['description'] ?? ''];
                }
            }

            // Zones (comptage)
            $zonesBrutes = (array) ($pageOrganisation->getTranslation('zones', $locale) ?? []);
            $zonesCount = count($zonesBrutes);
        }

        return view('pages.home', [
            'settings'           => $settings,
            'domaines'           => $domaines,
            'projets'            => $projets,
            'projetPhare'        => $projetPhare,
            'projetsRealises'    => $projetsRealises,
            'projetsAF'          => $projetsAF,
            'zonesCount'         => $zonesCount,
            'actualites'         => $actualites,
            'partenaires'        => $partenaires,
            'founder'            => $founder,
            'organisation'       => $organisation,
            'pageOrganisation'   => $pageOrganisation,
            'timeline'           => $timeline,
            'recognitions'       => $recognitions,
            'yearsOfActivity'    => $yearsOfActivity,
            'locale'             => $locale,
        ]);
    }
}
