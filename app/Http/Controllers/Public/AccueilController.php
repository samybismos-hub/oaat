<?php

namespace App\Http\Controllers\Public;

use App\Models\Actuality;
use App\Models\Domain;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Project;
use App\Models\Partner;

/**
 * AccueilController — Page d'accueil du site public.
 *
 * Rassemble les données de 6 tables :
 * - Domaines d'intervention (les 9)
 * - Projets à la une (is_featured = true)
 * - Actualités récentes (les 3 dernières publiées)
 * - Partenaires (tous, pour les logos)
 * - Chiffres clés (Settings : années, territoires, projets, bénéficiaires)
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
        // 1. Chiffres clés depuis les paramètres du site
        $settings = Setting::current();

        // 2. Domaines d'intervention (les 9, triés par nom)
        $domaines = Domain::query()
            ->orderBy('name')
            ->get();

        // 3. Projets à la une (jusqu'à 3, avec leur domaine)
        $projets = Project::query()
            ->where('is_featured', true)
            ->published()
            ->with('domain')
            ->latest('published_at')
            ->take(3)
            ->get();

        // 4. Actualités récentes (3 dernières publiées)
        $actualites = Actuality::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        // 5. Partenaires (tous, triés par nom)
        $partenaires = Partner::query()
            ->orderBy('name')
            ->get();

        // 6. Page d'organisation pour la mission
        $organisation = Page::query()
            ->where('slug', 'accueil')
            ->first();

        return view('pages.home', [
            'settings'      => $settings,
            'domaines'      => $domaines,
            'projets'       => $projets,
            'actualites'    => $actualites,
            'partenaires'   => $partenaires,
            'organisation'  => $organisation,
            'locale'        => $locale,
        ]);
    }
}