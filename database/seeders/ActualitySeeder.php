<?php

namespace Database\Seeders;

use App\Models\Actuality;
use Illuminate\Database\Seeder;

class ActualitySeeder extends Seeder
{
    /**
     * Actualités de démonstration créées en BROUILLON (published_at = null).
     *
     * IMPORTANT : le PDF de présentation du client ne contient AUCUNE actualité.
     * Ces deux entrées restent donc invisibles sur le site public tant que le client
     * ne les a pas validées puis publiées depuis l'administration.
     */
    public function run(): void
    {
        $actualities = [
            [
                'slug' => 'oaat-au-service-des-communautes-depuis-1995',
                'title' => [
                    'fr' => "L'OAAT au service des communautés de l'Est de la RD Congo depuis 1995",
                    'en' => 'OAAT serving communities in eastern DR Congo since 1995',
                ],
                'body' => [
                    'fr' => "<p>Depuis sa création, l'OAAT intervient aux côtés des communautés de l'Est de la République Démocratique du Congo dans les domaines de l'aménagement des territoires, de la sécurité alimentaire, de l'éducation, de la santé, de l'eau et de l'assainissement.</p><p>Retrouvez l'ensemble de nos domaines d'intervention et de nos projets dans les rubriques correspondantes du site.</p>",
                    'en' => '<p>Since its creation, OAAT has been working alongside communities in eastern Democratic Republic of Congo in the fields of land planning, food security, education, health, water and sanitation.</p><p>Discover all our areas of intervention and our projects in the dedicated sections of this website.</p>',
                ],
                'published_at' => null,
            ],
            [
                'slug' => 'projets-en-attente-de-financement',
                'title' => [
                    'fr' => 'Des projets en attente de financement',
                    'en' => 'Projects awaiting funding',
                ],
                'body' => [
                    'fr' => "<p>Plusieurs programmes d'aménagement des territoires, de sécurité alimentaire, d'appui aux écoles et d'accès à l'eau sont prêts et restent en attente de financement.</p><p>Les partenaires et bailleurs intéressés peuvent nous contacter via le formulaire de contact ou l'espace « Soumettre un besoin ».</p>",
                    'en' => '<p>Several programmes in land planning, food security, school support and water access are ready and remain awaiting funding.</p><p>Interested partners and funders can contact us through the contact form or the "Submit a need" section.</p>',
                ],
                'published_at' => null,
            ],
        ];

        foreach ($actualities as $actuality) {
            Actuality::updateOrCreate(['slug' => $actuality['slug']], $actuality);
        }
    }
}
