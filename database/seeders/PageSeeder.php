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
                    'fr' => 'L\'Organisation',
                    'en' => 'The Organisation',
                ],
                'body' => [
                    'fr' => '<!--TIMELINE-->
<div class="timeline-entry" data-year="1995" data-title="EAU, Espace et Aménagement Urbain">
<p>Fondée le 10 mai 1995 à l\'initiative de l\'Ingénieur Roger MANEMA CIRHAHINGIRWA, sous le nom d\'ONG EAU (Espace et Aménagement Urbain).</p>
</div>
<div class="timeline-entry" data-year="2004" data-title="EAUR, Urbain et Rural">
<p>L\'assemblée générale du 4 janvier 2004 ajoute la dimension rurale au nom de l\'organisation qui devient EAUR (Espace et Aménagement Urbain et Rural).</p>
</div>
<div class="timeline-entry" data-year="2006-2009" data-title="Reconnaissance et projets avec l\'ONU">
<p>Autorisation de fonctionnement (2006), identification MINIPLAN (2007), enregistrement (2009) ; ponts et routes avec le PNUD, eau potable avec le Pooled Fund.</p>
</div>
<div class="timeline-entry" data-year="2013" data-title="OAAT, Organisation Africaine pour l\'Aménagement des Territoires">
<p>À la demande de ses partenaires, l\'organisation élargit son rayon d\'action et adopte son nom actuel : Organisation Africaine pour l\'Aménagement des Territoires (OAAT).</p>
</div>
<div class="timeline-entry" data-year="2016-2024" data-title="Ouverture régionale et nouvelles réalisations">
<p>Forum sur le carbone à Kigali (IPBS, NAMA Facility, UNFCCC), ateliers SAFE à Kigali et Nairobi ; entrepôt de riz, pisciculture avec la FAO, école de Karhale et foyer social de Saké.</p>
</div>
<!--/TIMELINE-->

<h2>Mission</h2>
<p>L\'OAAT est une organisation non gouvernementale apolitique et non confessionnelle qui apporte son savoir-faire aux personnes et structures qui en ont besoin, afin de promouvoir le développement socio-économique et technique de l\'Est de la RD Congo.</p>

<h2>Objectif</h2>
<p>Renouer le lien entre le développement technologique et les besoins exprimés par les populations les plus défavorisées en RD Congo, à travers des actions d\'aménagement des territoires urbains et ruraux et la réalisation de projets sociaux.</p>

<h2>Zones d\'intervention</h2>
<!--ZONES-->
<div class="zones-data">
<div class="zone-entry" data-province="Sud-Kivu" data-name="Fizi"><p>Lac Tanganyika, Mutambala, Ngandja, Kimbi-Lulenge</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Uvira"><p>Plaine de la Ruzizi et moyens plateaux</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Walungu"><p>Kaniola, Mulamba, Burhale, Karhongo, Luchiga</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Mwenga"><p>Itombwe, Luhwinja, Burhinyi, Wamuzimu</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Kabare"><p>Nindja, Mudake, Katana, Mumosho</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Kalehe"><p>Minova, Kalonge, Kalehe, Bunyakiri</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Shabunda"><p>Bamuguba Sud et Nord, Baliga</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Goma"><p>Ville de Goma</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Karisimbi"><p>Ville de Karisimbi</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Nyiragongo"><p>Nyiragongo</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Masisi"><p>Sake</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Walikale"><p>Walikale centre</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Lubero"><p>Beni et Butembo</p></div>
</div>
<!--/ZONES-->

<!--RECOGNITIONS-->
<div class="recognition-item" data-title="Certificat de dépôt" data-authority="Ministère de la Justice, 27 janvier 2004">
Certificat de dépôt n° JUST.G.S. 112/S-KIVU/1592/2004 du 27 janvier 2004
</div>
<div class="recognition-item" data-title="Autorisation de fonctionnement" data-authority="Territoire d\'Uvira, 20 janvier 2006">
Autorisation de fonctionnement provisoire d\'une association du 20 janvier 2006
</div>
<div class="recognition-item" data-title="Certificat d\'identification d\'ONG" data-authority="Ministère du Plan, 2007">
Certificat d\'identification d\'ONG n° 69/MINIPLAN/DPP/NK/KMA/2007
</div>
<div class="recognition-item" data-title="Certificat d\'enregistrement" data-authority="2009">
Certificat d\'enregistrement n° TPI/PSK/DIV/69/2009
</div>
<div class="recognition-item" data-title="Certificat d\'enregistrement" data-authority="Ministère du Plan et Budget, 2013">
Certificat d\'enregistrement n° 21/013/GP/SK/CAB/MINIPLAN &amp; BUDGET/2013
</div>
<div class="recognition-item" data-title="Attestation provisoire d\'agrément" data-authority="Division des affaires humanitaires, 2013">
Attestation provisoire d\'agrément n° 08/002/DIVAH-SN/2013
</div>
<!--/RECOGNITIONS-->',
                    'en' => '<!--TIMELINE-->
<div class="timeline-entry" data-year="1995" data-title="EAU, Espace et Aménagement Urbain">
<p>Founded on 10 May 1995 on the initiative of Engineer Roger MANEMA CIRHAHINGIRWA, under the name EAU (Espace et Aménagement Urbain).</p>
</div>
<div class="timeline-entry" data-year="2004" data-title="EAUR, Urbain et Rural">
<p>The General Assembly of 4 January 2004 added the rural dimension to the organisation\'s name, which became EAUR (Espace et Aménagement Urbain et Rural).</p>
</div>
<div class="timeline-entry" data-year="2006-2009" data-title="Recognition and projects with the UN">
<p>Operating authorisation (2006), MINIPLAN identification (2007), registration (2009); bridges and roads with UNDP, drinking water with the Pooled Fund.</p>
</div>
<div class="timeline-entry" data-year="2013" data-title="OAAT, African Organisation for Land Planning">
<p>At the request of its partners, the organisation expanded its scope and adopted its current name: African Organisation for Land Planning (OAAT).</p>
</div>
<div class="timeline-entry" data-year="2016-2024" data-title="Regional outreach and new achievements">
<p>Carbon forum in Kigali (IPBS, NAMA Facility, UNFCCC), SAFE workshops in Kigali and Nairobi; rice warehouse, fish farming with FAO, Karhale school and Saké social home.</p>
</div>
<!--/TIMELINE-->

<h2>Mission</h2>
<p>OAAT is a non-governmental, apolitical and non-denominational organisation that shares its expertise with people and structures in need, in order to promote the socio-economic and technical development of eastern DR Congo.</p>

<h2>Objective</h2>
<p>To reconnect technological development with the needs expressed by the most disadvantaged populations in DR Congo, through land planning actions in urban and rural areas and the implementation of social projects.</p>

<h2>Areas of intervention</h2>
<!--ZONES-->
<div class="zones-data">
<div class="zone-entry" data-province="Sud-Kivu" data-name="Fizi"><p>Lake Tanganyika, Mutambala, Ngandja, Kimbi-Lulenge</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Uvira"><p>Ruzizi plain and middle plateaux</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Walungu"><p>Kaniola, Mulamba, Burhale, Karhongo, Luchiga</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Mwenga"><p>Itombwe, Luhwinja, Burhinyi, Wamuzimu</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Kabare"><p>Nindja, Mudake, Katana, Mumosho</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Kalehe"><p>Minova, Kalonge, Kalehe, Bunyakiri</p></div>
<div class="zone-entry" data-province="Sud-Kivu" data-name="Shabunda"><p>Bamuguba South and North, Baliga</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Goma"><p>Goma city</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Karisimbi"><p>Karisimbi city</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Nyiragongo"><p>Nyiragongo</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Masisi"><p>Sake</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Walikale"><p>Walikale centre</p></div>
<div class="zone-entry" data-province="Nord-Kivu" data-name="Lubero"><p>Beni and Butembo</p></div>
</div>
<!--/ZONES-->

<!--RECOGNITIONS-->
<div class="recognition-item" data-title="Deposit certificate" data-authority="Ministry of Justice, 27 January 2004">
Deposit certificate No. JUST.G.S. 112/S-KIVU/1592/2004 of 27 January 2004
</div>
<div class="recognition-item" data-title="Operating authorisation" data-authority="Uvira Territory, 20 January 2006">
Provisional operating authorisation of 20 January 2006
</div>
<div class="recognition-item" data-title="NGO identification certificate" data-authority="Ministry of Planning, 2007">
NGO identification certificate No. 69/MINIPLAN/DPP/NK/KMA/2007
</div>
<div class="recognition-item" data-title="Registration certificate" data-authority="2009">
Registration certificate No. TPI/PSK/DIV/69/2009
</div>
<div class="recognition-item" data-title="Registration certificate" data-authority="Ministry of Planning and Budget, 2013">
Registration certificate No. 21/013/GP/SK/CAB/MINIPLAN &amp; BUDGET/2013
</div>
<div class="recognition-item" data-title="Provisional accreditation certificate" data-authority="Division of Humanitarian Affairs, 2013">
Provisional accreditation certificate No. 08/002/DIVAH-SN/2013
</div>
<!--/RECOGNITIONS-->',
                ],
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}