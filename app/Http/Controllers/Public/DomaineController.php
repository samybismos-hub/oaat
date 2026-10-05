<?php

namespace App\Http\Controllers\Public;

use App\Models\Domain;

class DomaineController
{
    public function index(string $locale): mixed
    {
        $domaines = Domain::query()
            ->withCount('projects')
            ->orderBy('position')
            ->get();

        return view('pages.domaines-index', [
            'domaines' => $domaines,
            'locale'   => $locale,
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