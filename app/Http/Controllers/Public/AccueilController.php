<?php

namespace App\Http\Controllers\Public;

use App\Models\Actuality;
use App\Models\Domain;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Setting;

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

        // Extraction de la timeline et des reconnaissances depuis le body
        $timeline = [];
        $recognitions = [];
        if ($pageOrganisation) {
            $body = $pageOrganisation->getTranslation('body', $locale) ?? '';
            // Timeline : extraire chaque <div class="timeline-entry">
            preg_match_all(
                '/<div class="timeline-entry" data-year="([^"]*)" data-title="([^"]*)">\s*<p>(.*?)<\/p>/s',
                $body, $tlMatches, PREG_SET_ORDER
            );
            foreach ($tlMatches as $m) {
                $timeline[] = [$m[1], $m[2], trim(strip_tags($m[3]))];
            }
            // Reconnaissances : extraire chaque <div class="recognition-item">
            preg_match_all(
                '/<div class="recognition-item" data-title="([^"]*)" data-authority="([^"]*)">\s*(.*?)\s*<\/div>/s',
                $body, $recMatches, PREG_SET_ORDER
            );
            foreach ($recMatches as $m) {
                $recognitions[] = [$m[1], $m[2], trim(strip_tags($m[3]))];
            }
        }

        // ─── 12. Rendu de la vue ────────────────────────────────
        // Zones : comptage réel (fallback si le chiffre officiel n'est pas saisi)
        $zonesCount = 0;
        if ($pageOrganisation) {
            $zonesBody = $pageOrganisation->getTranslation('body', $locale) ?? '';
            preg_match_all('/data-province="([^"]*)"/', $zonesBody, $zonesMatches);
            $zonesCount = count($zonesMatches[1]);
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
            'organisation'       => $organisation,
            'pageOrganisation'   => $pageOrganisation,
            'timeline'           => $timeline,
            'recognitions'       => $recognitions,
            'yearsOfActivity'    => $yearsOfActivity,
            'locale'             => $locale,
        ]);
    }
}