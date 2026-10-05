<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProjectStatus;
use App\Models\Project;

class ProjetController
{
    public function index(string $locale): mixed
    {
        $projets = Project::query()
            ->published()
            ->with('domain')
            ->latest('published_at')
            ->get();

        $statuses = ProjectStatus::values();

        return view('pages.projets-index', [
            'projets'  => $projets,
            'statuses' => $statuses,
            'locale'   => $locale,
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