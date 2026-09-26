<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Données issues de "2.1 PRESENTATION DE OAAT ET SES EXPERIENCES.pdf" (section I. Identification).
     *
     * Volontairement laissés vides (à saisir / valider par le client) :
     * - socials : le PDF ne fournit AUCUNE URL de réseau social ;
     * - stat_projects / stat_beneficiaries / stat_years : aucun chiffre officiel fourni.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'organisation_name' => [
                    'fr' => "Organisation Africaine pour l'Aménagement des Territoires",
                    'en' => 'African Organisation for Land Planning',
                ],
                'email' => 'oaatrdc2000@gmail.com',
                'phones' => [
                    '+243 993 537 325',
                    '+243 896 127 195',
                ],
                'addresses' => [
                    'fr' => [
                        "Bureau de représentation : N° 007, Avenue Fizi II/Nyawera, Quartier Ndendere, Commune d'Ibanda, Bukavu, Sud-Kivu, RD Congo",
                        'Siège social : Nyangezi, Groupement de Karhongo, Territoire de Walungu, Sud-Kivu, RD Congo',
                    ],
                    'en' => [
                        'Representation office: No. 007, Fizi II/Nyawera Avenue, Ndendere district, Ibanda commune, Bukavu, South Kivu, DR Congo',
                        'Head office: Nyangezi, Karhongo grouping, Walungu territory, South Kivu, DR Congo',
                    ],
                ],
                'socials' => null,
                'stat_projects' => null,
                'stat_beneficiaries' => null,
                'stat_zones' => 13,
                'stat_years' => null,
            ]
        );
    }
}
