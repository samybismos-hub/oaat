<?php

namespace App\Http\Controllers\Public;

use App\Models\Partner;

class PartenaireController
{
    public function index(string $locale): mixed
    {
        $partenaires = Partner::query()
            ->orderBy('name')
            ->get();

        return view('pages.partenaires', [
            'partenaires' => $partenaires,
            'locale'      => $locale,
        ]);
    }
}