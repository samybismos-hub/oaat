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

        $compteurs =[
            'domaines' => $domaines->count(),
            'projets'  => (int) $projetsCount,
            'zones'    => (int) $zonesCount,
        ];

        return view('pages.domaines-index', [
            'domaines'     => $domaines,
            'projetsCount' => $projetsCount,
            'zonesCount'   => $zonesCount,
            'pageDomaines' => $pageDomaines,
            'locale'       => $locale,
            'compteurs'    => $compteurs
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
