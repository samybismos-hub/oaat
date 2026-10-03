<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Partner;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $projects = [
            [
                'slug' => 'ponts-kasandjala-i-et-ii',
                'domain' => 'logistique',                       // ← slug du domaine (pas domain_id)
                'title' => [
                    'fr' => 'Construction des ponts KASANDJALA I et II à Sebele',
                    'en' => 'Construction of the KASANDJALA I and II bridges in Sebele',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Sebele, Fizi',
                'start_date' => '2007-01-01',
                'end_date' => '2007-05-01',
                'objectives' => [
                    'fr' => 'Désenclaver la zone et faciliter la circulation des populations.',
                    'en' => 'Open up the area and facilitate the movement of people.',
                ],
                'beneficiaries' => [
                    'fr' => '15 800 personnes',
                    'en' => '15,800 people',
                ],
                'results' => [
                    'fr' => 'Deux ponts construits et mis en service.',
                    'en' => 'Two bridges built and commissioned.',
                ],
                'budget_amount' => 104918.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 15800,
                'beneficiaries_unit' => 'personnes',
                'is_featured' => true,
                'published_at' => '2007-05-01 00:00:00',
                'partners' => [
                    'PNUD' => 'funder',
                ],
            ],
            [
                'slug' => 'eau-potable-sebele-fizi',
                'domain' => 'wash',
                'title' => [
                    'fr' => 'Approvisionnement en eau potable et promotion de l\'hygiène à Sebele (Fizi)',
                    'en' => 'Drinking water supply and hygiene promotion in Sebele (Fizi)',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Sebele, Fizi',
                'start_date' => '2008-01-01',
                'end_date' => '2008-11-01',
                'objectives' => [
                    'fr' => 'Lutter contre les maladies d\'origine hydrique et améliorer l\'accès à l\'eau potable.',
                    'en' => 'Fight waterborne diseases and improve access to drinking water.',
                ],
                'beneficiaries' => [
                    'fr' => '22 000 personnes',
                    'en' => '22,000 people',
                ],
                'results' => [
                    'fr' => 'Approvisionnement en eau et promotion de l\'hygiène réalisés.',
                    'en' => 'Water supply and hygiene promotion completed.',
                ],
                'budget_amount' => 124985.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 22000,
                'beneficiaries_unit' => 'personnes',
                'is_featured' => false,
                'published_at' => '2008-11-01 00:00:00',
                'partners' => [
                    'Pooled Fund' => 'funder',
                ],
            ],
            [
                'slug' => 'forages-latrines-fizi',
                'domain' => 'wash',
                'title' => [
                    'fr' => 'Forages de 8 puits et assainissement à Fizi',
                    'en' => 'Drilling of 8 wells and sanitation in Fizi',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Fizi',
                'start_date' => '2009-01-01',
                'end_date' => '2009-07-01',
                'objectives' => [
                    'fr' => 'Réduire les maladies d\'origine hydrique et lutter contre le choléra.',
                    'en' => 'Reduce waterborne diseases and fight cholera.',
                ],
                'beneficiaries' => [
                    'fr' => '45 250 personnes',
                    'en' => '45,250 people',
                ],
                'results' => [
                    'fr' => 'Huit puits forés et ouvrages d\'assainissement réalisés.',
                    'en' => 'Eight wells drilled and sanitation works completed.',
                ],
                'budget_amount' => 110079.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 45250,
                'beneficiaries_unit' => 'personnes',
                'is_featured' => false,
                'published_at' => '2009-07-01 00:00:00',
                'partners' => [
                    'PNUD' => 'funder',
                ],
            ],
            [
                'slug' => 'route-sebele-kazimia',
                'domain' => 'logistique',
                'title' => [
                    'fr' => 'Réhabilitation de la route SEBELE – KAZIMIA',
                    'en' => 'Rehabilitation of the SEBELE – KAZIMIA road',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Fizi',
                'start_date' => '2010-01-01',
                'end_date' => '2011-01-01',
                'objectives' => [
                    'fr' => 'Réduire le désenclavement et permettre le rapatriement des réfugiés de Tanzanie.',
                    'en' => 'Reduce isolation and enable the repatriation of refugees from Tanzania.',
                ],
                'beneficiaries' => [
                    'fr' => '120 jeunes et 346 ménages',
                    'en' => '120 young people and 346 households',
                ],
                'results' => [
                    'fr' => 'Route réhabilitée et circulable par les ONG humanitaires.',
                    'en' => 'Road rehabilitated and usable by humanitarian NGOs.',
                ],
                'budget_amount' => 180500.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 466,
                'beneficiaries_unit' => 'bénéficiaires',
                'is_featured' => false,
                'published_at' => '2011-01-01 00:00:00',
                'partners' => [
                    'PNUD' => 'funder',
                ],
            ],
            [
                'slug' => 'latrines-familiales-sud-kivu',
                'domain' => 'wash',
                'title' => [
                    'fr' => 'Construction de 346 latrines familiales au Sud-Kivu',
                    'en' => 'Construction of 346 family latrines in South Kivu',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Sud-Kivu',
                'start_date' => '2013-01-01',
                'end_date' => '2013-07-01',
                'objectives' => [
                    'fr' => 'Lutter contre l\'insalubrité et les maladies d\'origine hydrique.',
                    'en' => 'Fight unsanitary conditions and waterborne diseases.',
                ],
                'beneficiaries' => [
                    'fr' => '250 filles et garçons',
                    'en' => '250 girls and boys',
                ],
                'results' => [
                    'fr' => '346 latrines familiales construites.',
                    'en' => '346 family latrines built.',
                ],
                'budget_amount' => 45550.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 250,
                'beneficiaries_unit' => 'personnes',
                'is_featured' => false,
                'published_at' => '2013-07-01 00:00:00',
                'partners' => [
                    'BETING' => 'implementer',
                ],
            ],
            [
                'slug' => 'route-numbi-ziralo',
                'domain' => 'logistique',
                'title' => [
                    'fr' => 'Réhabilitation de la route de 38 km Numbi – Ziralo (Kalehe)',
                    'en' => 'Rehabilitation of the 38 km Numbi – Ziralo road (Kalehe)',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Kalehe',
                'start_date' => '2014-01-01',
                'end_date' => '2014-12-01',
                'objectives' => [
                    'fr' => 'Faciliter l\'accès des humanitaires à la zone de santé de Minova.',
                    'en' => 'Facilitate humanitarian access to the Minova health zone.',
                ],
                'beneficiaries' => [
                    'fr' => '850 élèves',
                    'en' => '850 pupils',
                ],
                'results' => [
                    'fr' => '38 km de route réhabilités.',
                    'en' => '38 km of road rehabilitated.',
                ],
                'budget_amount' => 77850.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 850,
                'beneficiaries_unit' => 'élèves',
                'is_featured' => false,
                'published_at' => '2014-12-01 00:00:00',
                'partners' => [
                    'BETING' => 'implementer',
                ],
            ],
            [
                'slug' => 'autonomisation-jeunes-goma',
                'domain' => 'formation-professionnelle',
                'title' => [
                    'fr' => 'Autonomisation économique des jeunes à Goma',
                    'en' => 'Economic empowerment of young people in Goma',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Goma',
                'start_date' => '2017-01-01',
                'end_date' => '2017-12-01',
                'objectives' => [
                    'fr' => 'Former les jeunes et les accompagner vers l\'auto-emploi.',
                    'en' => 'Train young people and support them towards self-employment.',
                ],
                'beneficiaries' => [
                    'fr' => '300 jeunes',
                    'en' => '300 young people',
                ],
                'results' => [
                    'fr' => '300 jeunes formés à l\'entrepreneuriat.',
                    'en' => '300 young people trained in entrepreneurship.',
                ],
                'budget_amount' => 48994.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 300,
                'beneficiaries_unit' => 'jeunes',
                'is_featured' => false,
                'published_at' => '2017-12-01 00:00:00',
                'partners' => [
                    'UNICEF' => 'funder',
                ],
            ],
            [
                'slug' => 'pisciculture-tilapia-karhongo',
                'domain' => 'securite-alimentaire',
                'title' => [
                    'fr' => 'Production de poissons Tilapia en étangs piscicoles (Karhongo)',
                    'en' => 'Tilapia fish production in fish ponds (Karhongo)',
                ],
                'status' => 'completed',
                'country' => 'RD Congo',
                'city' => 'Karhongo, Walungu',
                'start_date' => '2023-01-01',
                'end_date' => '2023-12-01',
                'objectives' => [
                    'fr' => 'Diversifier les sources de revenus et améliorer l\'alimentation des ménages.',
                    'en' => 'Diversify income sources and improve household nutrition.',
                ],
                'beneficiaries' => [
                    'fr' => '560 vendeuses de poissons',
                    'en' => '560 fish sellers',
                ],
                'results' => [
                    'fr' => 'Étangs piscicoles opérationnels et production de Tilapia lancée.',
                    'en' => 'Operational fish ponds and Tilapia production started.',
                ],
                'budget_amount' => 47983.10,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 560,
                'beneficiaries_unit' => 'vendeuses de poissons',
                'is_featured' => true,
                'published_at' => '2023-12-01 00:00:00',
                'partners' => [
                    'FAO' => 'funder',
                ],
            ],
            [
                'slug' => 'ecole-karhale-kadutu',
                'domain' => 'education',
                'title' => [
                    'fr' => 'Construction d\'une école primaire et secondaire à Karhale (Kadutu)',
                    'en' => 'Construction of a primary and secondary school in Karhale (Kadutu)',
                ],
                'status' => 'ongoing',
                'country' => 'RD Congo',
                'city' => 'Karhale, Kadutu',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'objectives' => [
                    'fr' => 'Offrir un cadre scolaire adéquat aux enfants de la commune de Kadutu.',
                    'en' => 'Provide an adequate school environment for children in Kadutu commune.',
                ],
                'beneficiaries' => [
                    'fr' => '2 500 élèves',
                    'en' => '2,500 pupils',
                ],
                'results' => [
                    'fr' => 'École à deux niveaux et bâtiment communautaire en cours de construction.',
                    'en' => 'Two-level school and community building under construction.',
                ],
                'budget_amount' => 450000.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 2500,
                'beneficiaries_unit' => 'élèves',
                'is_featured' => false,
                'published_at' => '2024-01-15 00:00:00',
                'partners' => [
                    'BETING' => 'funder',
                ],
            ],
            [
                'slug' => 'jeunes-filles-meres-sake',
                'domain' => 'genre',
                'title' => [
                    'fr' => 'Appui et encadrement des jeunes filles mères à Saké (Nord-Kivu)',
                    'en' => 'Support and mentoring of young mothers in Sake (North Kivu)',
                ],
                'status' => 'ongoing',
                'country' => 'RD Congo',
                'city' => 'Saké, Masisi',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'objectives' => [
                    'fr' => 'Accompagner les jeunes filles mères vers l\'autonomisation économique et sociale.',
                    'en' => 'Support young mothers towards economic and social empowerment.',
                ],
                'beneficiaries' => [
                    'fr' => '200 filles',
                    'en' => '200 girls',
                ],
                'results' => [
                    'fr' => 'Encadrement et appui aux jeunes filles mères en cours.',
                    'en' => 'Mentoring and support for young mothers in progress.',
                ],
                'budget_amount' => 80000.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 200,
                'beneficiaries_unit' => 'filles',
                'is_featured' => false,
                'published_at' => '2024-01-20 00:00:00',
                'partners' => [
                    'BETING' => 'funder',
                ],
            ],
            [
                'slug' => 'amenagement-des-8-territoires',
                'domain' => 'logistique',
                'title' => [
                    'fr' => 'Projet d\'aménagement des huit territoires',
                    'en' => 'Project for the development of the eight territories',
                ],
                'status' => 'awaiting_funding',
                'country' => 'RD Congo',
                'city' => 'Sud-Kivu et Nord-Kivu',
                'start_date' => null,
                'end_date' => null,
                'objectives' => [
                    'fr' => 'Réhabiliter les routes de desserte agricole, construire un barrage hydro-électrique et photovoltaïque, construire des micro-centrales et des centres d\'apprentissage de métiers dans les 8 territoires.',
                    'en' => 'Rehabilitate agricultural access roads, build a hydro-electric and photovoltaic dam, build micro-power plants and vocational training centres in the 8 territories.',
                ],
                'beneficiaries' => [
                    'fr' => '6 349 200 habitants',
                    'en' => '6,349,200 inhabitants',
                ],
                'results' => [
                    'fr' => 'Projet en attente de financement.',
                    'en' => 'Project awaiting funding.',
                ],
                'budget_amount' => 541591.00,
                'budget_currency' => 'USD',
                'beneficiaries_count' => 6349200,
                'beneficiaries_unit' => 'habitants',
                'is_featured' => true,
                'published_at' => null,
                'partners' => [
                    'IPBS' => 'funder',
                    'NAMA Facility' => 'funder',
                ],
            ],
        ];
        foreach ($projects as $data) {
            $domain = Domain::where('slug', $data['domain'])->firstOrFail();
            $partners = $data['partners'] ?? [];

            unset($data['domain'], $data['partners']);

            $project = Project::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['domain_id' => $domain->id])
            );

            foreach ($partners as $partnerName => $role) {
                $partner = Partner::where('name', $partnerName)->firstOrFail();

                $project->partners()->syncWithoutDetaching([$partner->id => ['role' => $role]]);
            }
        }

    }
}
