<?php

namespace Database\Seeders;

use App\Models\Album;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Un album de galerie vide, prêt à recevoir les photos.
     *
     * Les photos ne sont PAS semées automatiquement : elles doivent être téléversées
     * depuis l'administration (Filament) pour que le client garde la maîtrise des images publiées.
     *
     * La table « albums » porte désormais une colonne unique (slug) : le seeder
     * ne recrée donc rien tant qu'un album existe déjà.
     */
    public function run(): void
    {
        if (Album::count() > 0) {
            return;
        }

        Album::create([
            'title' => [
                'fr' => 'Album de démonstration',
                'en' => 'Demonstration album',
            ],
            'slug' => 'album-de-demonstration',
            'project_id' => null,
        ]);
    }
}
