<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Contenu statique bilingue, rédigé à partir du PDF de présentation OAAT.
     */
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
                    'fr' => 'L\'Organisation',
                    'en' => 'The Organisation',
                ],
                'body' => [
                    'fr' => "<h2>Historique</h2><p>Créée le 10 mai 1995 à l'initiative de l'Ingénieur Roger MANEMA CIRHAHINGIRWA sous le nom d'ONG EAU (Espace et Aménagement Urbain), l'organisation devient EAUR (Espace et Aménagement Urbain et Rural) en 2004, avant d'adopter son nom actuel, Organisation Africaine pour l'Aménagement des Territoires (OAAT), en janvier 2013.</p><h2>Mission</h2><p>L'OAAT est une organisation non gouvernementale apolitique et non confessionnelle qui apporte son savoir-faire aux personnes et structures qui en ont besoin, afin de promouvoir le développement socio-économique et technique de l'Est de la RD Congo.</p><h2>Objectif</h2><p>Renouer le lien entre le développement technologique et les besoins exprimés par les populations les plus défavorisées en RD Congo, à travers des actions d'aménagement des territoires urbains et ruraux et la réalisation de projets sociaux.</p><h2>Zones d\'intervention</h2><ul><li>Sud-Kivu : Fizi, Uvira, Walungu, Mwenga, Kabare, Kalehe, Shabunda</li><li>Nord-Kivu : Goma, Karisimbi, Nyiragongo, Masisi, Walikale, Lubero</li></ul><h2>Reconnaissances officielles</h2><ul><li>Certificat de dépôt n° JUST.G.S. 112/S-KIVU/1592/2004 du 27 janvier 2004</li><li>Autorisation de fonctionnement provisoire d\'une association du 20 janvier 2006</li><li>Attestation provisoire d\'enregistrement n° TPI/PSK/DIV/69/2009</li><li>Attestation provisoire d\'agrément n° 08/002/DIVAH-SN/2013</li></ul>",
                    'en' => '<h2>History</h2><p>Created on 10 May 1995 on the initiative of Engineer Roger MANEMA CIRHAHINGIRWA under the name EAU (Espace et Amenagement Urbain), the organisation became EAUR (Espace et Amenagement Urbain et Rural) in 2004, before adopting its current name, African Organisation for Land Planning (OAAT), in January 2013.</p><h2>Mission</h2><p>OAAT is a non-governmental, apolitical and non-denominational organisation that shares its expertise with people and structures in need, in order to promote the socio-economic and technical development of eastern DR Congo.</p><h2>Objective</h2><p>To reconnect technological development with the needs expressed by the most disadvantaged populations in DR Congo, through land planning actions in urban and rural areas and the implementation of social projects.</p><h2>Areas of intervention</h2><ul><li>South Kivu: Fizi, Uvira, Walungu, Mwenga, Kabare, Kalehe, Shabunda</li><li>North Kivu: Goma, Karisimbi, Nyiragongo, Masisi, Walikale, Lubero</li></ul><h2>Official accreditations</h2><ul><li>Deposit certificate No. JUST.G.S. 112/S-KIVU/1592/2004 of 27 January 2004</li><li>Provisional operating authorisation of 20 January 2006</li><li>Provisional registration certificate No. TPI/PSK/DIV/69/2009</li><li>Provisional accreditation certificate No. 08/002/DIVAH-SN/2013</li></ul>',
                ],
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
