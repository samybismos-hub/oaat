<?php

namespace App\Http\Controllers\Public;

use App\Models\Document;

class DocumentController
{
    public function index(string $locale): mixed
    {
        $documents = Document::query()
            ->published()
            ->latest('published_at')
            ->get();

        return view('pages.documents-index', [
            'documents' => $documents,
            'locale'    => $locale,
        ]);
    }
}