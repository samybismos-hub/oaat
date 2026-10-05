<?php

namespace App\Http\Controllers\Public;

use App\Models\Album;

class GalerieController
{
    public function index(string $locale): mixed
    {
        $albums = Album::query()
            ->latest('created_at')
            ->get();

        return view('pages.galerie-index', [
            'albums' => $albums,
            'locale' => $locale,
        ]);
    }

    public function show(string $locale, string $slug): mixed
    {
        $album = Album::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.galerie-show', [
            'album' => $album,
            'locale' => $locale,
        ]);
    }
}