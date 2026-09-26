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
     * La table "albums" n'a pas de colonne unique : le seeder ne s'exécute donc
     * qu'une seule fois (si la table est vide).
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
            'project_id' => null,
        ]);
    }
}
