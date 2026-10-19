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

       // ─── 7. Timeline, reconnaissances et zones depuis les colonnes JSON ─
        $timeline = [];
        foreach ((array) $pageOrganisation->timelineFor($locale) as $item) {
            $timeline[] = [$item['year'] ?? '', $item['title'] ?? '', $item['description'] ?? ''];
        }

        $recognitions = [];
        foreach ((array) $pageOrganisation->recognitionsFor($locale) as $item) {
            $recognitions[] = [$item['title'] ?? '', $item['authority'] ?? '', $item['description'] ?? ''];
        }

        $zones = [];
        foreach ((array) $pageOrganisation->zonesFor($locale) as $item) {
            $province = $item['province'] ?? (string) $item['province'] ?? '';
            $zones[$province][] = [$item['name'] ?? '', $item['description'] ?? ''];
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
            'zones'            => $zones,
            'locale'           => $locale,
        ]);
    }
}
