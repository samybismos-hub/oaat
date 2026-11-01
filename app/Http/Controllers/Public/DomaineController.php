<?php

namespace App\Http\Controllers\Public;

use App\Models\Domain;
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

        return view('pages.domaines-index', [
            'domaines'     => $domaines,
            'projetsCount' => $projetsCount,
            'zonesCount'   => $zonesCount,
            'locale'       => $locale,
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
