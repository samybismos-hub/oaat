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
 * Rassemble les données de 8 tables :
 * - Domaines d'intervention     (les 9)
 * - Projets à la une            (is_featured = true, jusqu'à 3)
 * - Tous les projets            (pour les compteurs de la section chiffres)
 * - Actualités récentes         (les 3 dernières publiées)
 * - Partenaires                 (tous, triés par nom, pour les logos)
 * - Équipe                      (membres triés par position)
 * - Chiffres clés               (Settings : années, territoires, total projets, total bénéficiaires)
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

        // ─── 4. Tous les projets publiés (pour les compteurs) ─
        $tousProjets = Project::query()
            ->published()
            ->with('domain')
            ->get();

        // Total des bénéficiaires (somme sur tous les projets publiés)
        $totalBeneficiaires = 0;
        foreach ($tousProjets as $p) {
            $totalBeneficiaires += (int) ($p->beneficiaries_count ?? 0);
        }

        // ─── 5. Actualités récentes (3 dernières publiées) ────
        $actualites = Actuality::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        // ─── 6. Partenaires (tous, triés par nom) ─────────────
        $partenaires = Partner::query()
            ->orderBy('name')
            ->get();

        // ─── 7. Membres de l'équipe (triés par position) ─────
        $equipe = TeamMember::query()
            ->orderBy('position')
            ->get();

        // ─── 8. Années d'activité (calculée depuis la fondation) ─
        $yearsOfActivity = (int) now()->diffInYears(config('oaat.founded_at', '1995-05-10'), true);

        // ─── 9. Page d'accueil pour le hero et le SEO ────────
        $organisation = Page::query()
            ->where('slug', 'accueil')
            ->first();

        // ─── 10. Page Organisation pour la section Mission ─────
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

        // ─── 10. Rendu de la vue ───────────────────────────────
        return view('pages.home', [
            'settings'           => $settings,
            'domaines'           => $domaines,
            'projets'            => $projets,
            'tousProjets'        => $tousProjets,
            'totalBeneficiaires' => $totalBeneficiaires,
            'actualites'         => $actualites,
            'partenaires'        => $partenaires,
            'equipe'             => $equipe,
            'organisation'       => $organisation,
            'pageOrganisation'   => $pageOrganisation,
            'timeline'           => $timeline,
            'recognitions'       => $recognitions,
            'yearsOfActivity'    => $yearsOfActivity,
            'locale'             => $locale,
        ]);
    }
}