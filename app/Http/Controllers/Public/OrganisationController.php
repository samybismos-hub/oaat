<?php

namespace App\Http\Controllers\Public;

use App\Models\Page;
use App\Models\Setting;
use App\Models\TeamMember;

/**
 * OrganisationController — Page "Notre organisation".
 *
 * Affiche le contenu CMS de la page slug='organisation',
 * les membres de l'équipe, et les chiffres clés.
 */
class OrganisationController
{
    public function index(string $locale): mixed
    {
        $organisation = Page::query()
            ->where('slug', 'organisation')
            ->firstOrFail();

        $settings = Setting::current();

        $team = TeamMember::query()
            ->orderBy('position')
            ->get();

        return view('pages.organisation', [
            'organisation'  => $organisation,
            'settings'      => $settings,
            'team'          => $team,
            'locale'        => $locale,
        ]);
    }
}