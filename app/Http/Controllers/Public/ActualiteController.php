<?php

namespace App\Http\Controllers\Public;

use App\Models\Actuality;

class ActualiteController
{
    public function index(string $locale): mixed
    {
        $actualites = Actuality::query()
            ->published()
            ->latest('published_at')
            ->get();

        return view('pages.actualites-index', [
            'actualites' => $actualites,
            'locale'     => $locale,
        ]);
    }

    public function show(string $locale, string $slug): mixed
    {
        $actualite = Actuality::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.actualites-show', [
            'actualite' => $actualite,
            'locale'    => $locale,
        ]);
    }
}