<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'accueil',
                'title' => [
                    'fr' => 'Bienvenue à l\'OAAT',
                    'en' => 'Welcome to OAAT',
                ],
                'body' => [
                    'fr' => "<p>L'Organisation Africaine pour l'Aménagement des Territoires (OAAT) est une organisation sans but lucratif qui intervient à l'Est de la République Démocratique du Congo depuis 1995.</p><p>Elle œuvre pour la résilience des communautés, la stabilité socio-économique et culturelle ainsi que la paix durable dans la région des Grands Lacs.</p>",
                    'en' => '<p>The African Organisation for Land Planning (OAAT) is a non-profit organisation working in eastern Democratic Republic of Congo since 1995.</p><p>It works for community resilience, socio-economic and cultural stability, and lasting peace in the Great Lakes region.</p>',
                ],
            ],
            [
                'slug' => 'organisation',
                'title' => [
                    'fr' => "L'Organisation",
                    'en' => 'The Organisation',
                ],
                'body' => [
                    'fr' => '<p>L\'Organisation Africaine pour l\'Aménagement des Territoires (OAAT) est une organisation sans but lucratif qui intervient à l\'Est de la RD Congo depuis 1995. Elle œuvre pour la résilience des communautés, la stabilité socio-économique et culturelle ainsi que la paix durable dans la région des Grands Lacs.</p>
            <h2>Mission</h2>
            <p>Apporter notre savoir-faire aux personnes, ONG et entreprises de notre rayon d\'action pour promouvoir le développement socio-économique et technique.</p>
            <h2>Objectif</h2>
            <p>Renouer le lien entre le développement technologique et les besoins exprimés par les populations les plus défavorisées en RD Congo.</p>
            <h2>Zones d\'intervention</h2>
            <p>Treize zones réparties entre le Sud-Kivu et le Nord-Kivu.</p>',
                    'en' => '<p>The African Organisation for Land Planning (OAAT) is a non-profit organisation working in eastern DR Congo since 1995. It works for community resilience, socio-economic and cultural stability, and lasting peace in the Great Lakes region.</p>
            <h2>Mission</h2>
            <p>Bring our know-how to people, NGOs and businesses in our area of action to promote socio-economic and technical development.</p>
            <h2>Objective</h2>
            <p>Reconnect technological development with the needs expressed by the most disadvantaged people in DR Congo.</p>
            <h2>Areas of intervention</h2>
            <p>Thirteen areas spread across South Kivu and North Kivu.</p>',
                ],
                'timeline' => [
                    'fr' => [
                        ['year' => '1995', 'title' => 'EAU, Espace et Aménagement Urbain', 'description' => "Fondée le 10 mai 1995 à l'initiative de l'Ingénieur Roger MANEMA CIRHAHINGIRWA, sous le nom d'ONG EAU (Espace et Aménagement Urbain)."],
                        ['year' => '2004', 'title' => 'EAUR, Urbain et Rural', 'description' => "L'assemblée générale du 4 janvier 2004 ajoute la dimension rurale au nom de l'organisation qui devient EAUR (Espace et Aménagement Urbain et Rural)."],
                        ['year' => '2006-2009', 'title' => 'Reconnaissance et projets avec l\'ONU', 'description' => 'Autorisation de fonctionnement (2006), identification MINIPLAN (2007), enregistrement (2009) ; ponts et routes avec le PNUD, eau potable avec le Pooled Fund.'],
                        ['year' => '2013', 'title' => 'OAAT, Organisation Africaine pour l\'Aménagement des Territoires', 'description' => "À la demande de ses partenaires, l'organisation élargit son rayon d'action et adopte son nom actuel : Organisation Africaine pour l'Aménagement des Territoires (OAAT)."],
                        ['year' => '2016-2024', 'title' => 'Ouverture régionale et nouvelles réalisations', 'description' => 'Forum sur le carbone à Kigali (IPBS, NAMA Facility, UNFCCC), ateliers SAFE à Kigali et Nairobi ; entrepôt de riz, pisciculture avec la FAO, école de Karhale et foyer social de Saké.'],
                    ],
                    'en' => [
                        ['year' => '1995', 'title' => 'Water, Space and Urban Development', 'description' => 'Founded on 10 May 1995 on the initiative of Engineer Roger MANEMA CIRHAHINGIRWA, under the name EAU NGO (Water and Urban Development).'],
                        ['year' => '2004', 'title' => 'EAUR, Urban and Rural', 'description' => 'The General Assembly of 4 January 2004 added the rural dimension to the organisation\'s name, which became EAUR (Urban and Rural Development).'],
                        ['year' => '2006-2009', 'title' => 'Recognition and projects with the UN', 'description' => 'Operating authorisation (2006), MINIPLAN identification (2007), registration (2009); bridges and roads with UNDP, drinking water with the Pooled Fund.'],
                        ['year' => '2013', 'title' => 'OAAT, African Organisation for Land Planning', 'description' => "At the request of its partners, the organisation expanded its scope and adopted its current name: African Organisation for Land Planning (OAAT)."],
                        ['year' => '2016-2024', 'title' => 'Regional opening and new achievements', 'description' => 'Carbon forum in Kigali (IPBS, NAMA Facility, UNFCCC), SAFE workshops in Kigali and Nairobi; rice warehouse, fish farming with FAO, Karhale school and Saké social centre.'],
                    ],
                ],
                'recognitions' => [
                    'fr' => [
                        ['title' => 'Certificat de dépôt', 'authority' => 'Ministère de la Justice, 27 janvier 2004', 'description' => "Certificat de dépôt n° JUST.G.S. 112/S-KIVU/1592/2004 du 27 janvier 2004"],
                        ['title' => 'Autorisation de fonctionnement', 'authority' => 'Territoire d\'Uvira, 20 janvier 2006', 'description' => 'Autorisation provisoire de fonctionnement du 20 janvier 2006'],
                        ['title' => "Fiche d'identification ONG", 'authority' => 'Ministère du Plan, 2007', 'description' => "Fiche d'identification ONG n° 69/MINIPLAN/DPP/NK/KMA/2007"],
                        ['title' => 'Certificat d\'enregistrement', 'authority' => '2009', 'description' => 'Certificat d\'enregistrement n° TPI/PSK/DIV/69/2009'],
                        ['title' => 'Certificat d\'enregistrement', 'authority' => 'Ministère du Plan et du Budget, 2013', 'description' => "Certificat d'enregistrement n° 21/013/GP/SK/CAB/MINIPLAN & BUDGET/2013"],
                        ['title' => "Attestation d'agrément provisoire", 'authority' => 'Division des Affaires Humanitaires, 2013', "description" => "Attestation provisoire d'agrément n° 08/002/DIVAH-SN/2013"],
                    ],
                    'en' => [
                        ['title' => 'Deposit certificate', 'authority' => 'Ministry of Justice, 27 January 2004', 'description' => 'Deposit certificate No. JUST.G.S. 112/S-KIVU/1592/2004 of 27 January 2004'],
                        ['title' => 'Operating authorisation', 'authority' => 'Uvira Territory, 20 January 2006', 'description' => 'Provisional operating authorisation of 20 January 2006'],
                        ['title' => "NGO identification form", 'authority' => 'Ministry of Planning, 2007', 'description' => "NGO identification form No. 69/MINIPLAN/DPP/NK/KMA/2007"],
                        ['title' => 'Registration certificate', 'authority' => '2009', 'description' => 'Registration certificate No. TPI/PSK/DIV/69/2009'],
                        ['title' => 'Registration certificate', 'authority' => 'Ministry of Planning and Budget, 2013', 'description' => "Registration certificate No. 21/013/GP/SK/CAB/MINIPLAN & BUDGET/2013"],
                        ['title' => "Provisional accreditation certificate", 'authority' => 'Division of Humanitarian Affairs, 2013', "description" => "Provisional accreditation certificate No. 08/002/DIVAH-SN/2013"],
                    ],
                ],
                'zones' => [
                    'fr' => [
                        ['province' => 'Sud-Kivu', 'name' => 'Fizi', 'description' => 'Lac Tanganyika, Mutambala, Ngandja, Kimbi-Lulenge'],
                        ['province' => 'Sud-Kivu', 'name' => 'Uvira', 'description' => 'Plaine de la Ruzizi et moyens plateaux'],
                        ['province' => 'Sud-Kivu', 'name' => 'Walungu', 'description' => 'Kaniola, Mulamba, Burhale, Karhongo, Luchiga'],
                        ['province' => 'Sud-Kivu', 'name' => 'Mwenga', 'description' => 'Itombwe, Luhwinja, Burhinyi, Wamuzimu'],
                        ['province' => 'Sud-Kivu', 'name' => 'Kabare', 'description' => 'Nindja, Mudake, Katana, Mumosho'],
                        ['province' => 'Sud-Kivu', 'name' => 'Kalehe', 'description' => 'Minova, Kalonge, Kalehe, Bunyakiri'],
                        ['province' => 'Sud-Kivu', 'name' => 'Shabunda', 'description' => 'Bamuguba Sud et Nord, Baliga'],
                        ['province' => 'Nord-Kivu', 'name' => 'Goma', 'description' => "Ville de Goma"],
                        ['province' => 'Nord-Kivu', 'name' => 'Karisimbi', 'description' => "Ville de Karisimbi"],
                        ['province' => 'Nord-Kivu', 'name' => "Nyiragongo", "description" => "Nyiragongo"],
                        ['province' => "Nord-Kivu", "name" => "Masisi", "description" => "Sake"],
                        ['province' => "Nord-Kivu", "name" => "Walikale", "description" => "Walikale centre"],
                        ['province' => 'Nord-Kivu', 'name' => 'Lubero', 'description' => 'Beni and Butembo'],
                    ],
                    'en' => [
                        ['province' => 'Sud-Kivu', 'name' => 'Fizi', 'description' => 'Lake Tanganyika, Mutambala, Ngandja, Kimbi-Lulenge'],
                        ['province' => 'Sud-Kivu', 'name' => 'Uvira', 'description' => 'Ruzizi plain and middle plateaux'],
                        ['province' => 'Sud-Kivu', 'name' => 'Walungu', 'description' => 'Kaniola, Mulamba, Burhale, Karhongo, Luchiga'],
                        ['province' => 'Sud-Kivu', 'name' => 'Mwenga', 'description' => 'Itombwe, Luhwinja, Burhinyi, Wamuzimu'],
                        ['province' => 'Sud-Kivu', 'name' => 'Kabare', 'description' => 'Nindja, Mudake, Katana, Mumosho'],
                        ['province' => 'Sud-Kivu', 'name' => 'Kalehe', 'description' => 'Minova, Kalonge, Kalehe, Bunyakiri'],
                        ['province' => 'Sud-Kivu', 'name' => 'Shabunda', 'description' => "Bamuguba South and North, Baliga"],
                        ['province' => "Nord-Kivu", "name" => "Goma", "description" => "Goma city"],
                        ['province' => "Nord-Kivu", "name" => "Karisimbi", "description" => "Karisimbi city"],
                        ['province' => "Nord-Kivu", "name" => "Nyiragongo", "description" => "Nyiragongo"],
                        ['province' => "Nord-Kivu", "name" => "Masisi", "description" => "Sake"],
                        ['province' => "Nord-Kivu", "name" => "Walikale", "description" => "Walikale centre"],
                        ['province' => "Nord-Kivu", "name" => "Lubero", "description" => "Beni and Butembo"]
                    ],
                ],
            ],

            [
                'slug' => 'projets',
                'title' => [
                    'fr' => 'Projets - OAAT',
                    'en' => 'Projects - OAAT',
                ],
                'body' => [
                    'fr' => 'Trente ans de réalisations aux côtés des communautés, avec les agences des Nations Unies et des ONG internationales, et des projets prêts à être financés.',
                    'en' => 'Thirty years of achievements alongside communities, with UN agencies and international NGOs, and projects ready for funding.',
                ],
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
