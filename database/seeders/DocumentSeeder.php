<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Documents de référence créés en BROUILLON (published_at = null) et SANS fichier :
     * le client téléversera les PDF correspondants depuis l'administration.
     *
     * La table "documents" n'a pas de colonne unique : le seeder ne s'exécute donc
     * qu'une seule fois (si la table est vide), pour éviter les doublons.
     */
    public function run(): void
    {
        if (Document::count() > 0) {
            return;
        }

        $documents = [
            [
                'title' => [
                    'fr' => "Présentation de l'OAAT et de ses expériences avec les partenaires",
                    'en' => 'Presentation of OAAT and its experience with partners',
                ],
                'type' => 'rapport',
            ],
            [
                'title' => [
                    'fr' => "Reconnaissances officielles de l'organisation",
                    'en' => 'Official accreditations of the organisation',
                ],
                'type' => 'attestation',
            ],
            [
                'title' => [
                    'fr' => "Projets en attente de financement : catalogue",
                    'en' => 'Projects awaiting funding: catalogue',
                ],
                'type' => 'etude',
            ],
        ];

        foreach ($documents as $document) {
            Document::create([
                'title' => $document['title'],
                'type' => $document['type'],
                'published_at' => null,
            ]);
        }
    }
}
