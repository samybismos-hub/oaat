<?php

namespace App\Http\Controllers\Public;

use App\Models\Domain;
use App\Models\Page;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TeamMember;

/**
 * OrganisationController — Page "Notre organisation".
 *
 * Rassemble les données de la page slug='organisation',
 * du fondateur, des chiffres clés, de la timeline et des
 * reconnaissances officielles extraites du body CMS.
 */
class OrganisationController
{
    public function index(string $locale): mixed
    {
        // ─── 1. Page CMS "organisation" ──────────────────────────
        $pageOrganisation = Page::query()
            ->where('slug', 'organisation')
            ->firstOrFail();

        // ─── 2. Paramètres généraux du site ──────────────────────
        $settings = Setting::current();

        // ─── 3. Fondateur (premier membre marqué comme fondateur) ─
        $founder = TeamMember::query()
            ->where('is_founder', true)
            ->first();

        // ─── 4. Années d'activité (calculée depuis la fondation) ─
        $yearsOfActivity = (int) now()->diffInYears(config('oaat.founded_at', '1995-05-10'), true);

        // ─── 5. Total projets réalisés (publiés) ─────────────────
        $totalProjects = Project::query()
            ->published()
            ->count();

        // ─── 6. Nombre de domaines d'intervention ────────────────
        $domainCount = Domain::query()->count();

        // ─── 7. Extraction timeline + reconnaissances depuis le body ─
        $timeline = [];
        $recognitions = [];
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

        // ─── 8. Rendu de la vue ─────────────────────────────────
        return view('pages.organisation', [
            'pageOrganisation' => $pageOrganisation,
            'settings'         => $settings,
            'founder'          => $founder,
            'yearsOfActivity'  => $yearsOfActivity,
            'totalProjects'    => $totalProjects,
            'domainCount'      => $domainCount,
            'timeline'         => $timeline,
            'recognitions'     => $recognitions,
            'locale'           => $locale,
        ]);
    }
}