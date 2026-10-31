<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $domains = [
            [
                'slug' => 'sante',
                'name' => ['fr' => 'Santé', 'en' => 'Health'],
                'objectives' => [
                    'fr' => 'Améliorer l\'accès aux soins de santé des populations de l\'Est de la RD Congo.',
                    'en' => 'Improve access to healthcare for populations in eastern DR Congo.',
                ],
                'activities' => [
                    'fr' => 'Réhabilitation des infrastructures médicales et sanitaires ; fourniture de matériels et d\'intrants médicaux ; formation des personnels soignants.',
                    'en' => 'Rehabilitation of medical and health facilities; supply of medical equipment and inputs; training of care staff.',
                ],
                'icon' => 'heart-pulse',
                'intro' => [
                    'fr' => 'Réhabilitation de centres de santé, fourniture de matériel médical, formation du personnel soignant.',
                    'en' => 'Rehabilitation of health centres, supply of medical equipment, and training of care staff.',
                ],
                'interventions' => [
                    'fr' => [
                        'Réhabilitation d’infrastructures médicales et sanitaires',
                        'Fourniture de matériels et d’intrants médicaux',
                        'Formation des personnels soignants',
                    ],
                    'en' => [
                        'Rehabilitation of medical and health infrastructure',
                        'Supply of medical equipment and inputs',
                        'Training of care staff',
                    ],
                ],
                'formations' => [
                    'fr' => [],
                    'en' => [],
                ],
                'position' => 1,
            ],
            [
                'slug' => 'securite-alimentaire',
                'name' => ['fr' => 'Sécurité alimentaire', 'en' => 'Food security'],
                'objectives' => [
                    'fr' => 'Garantir la sécurité alimentaire des ménages agricoles et des populations vulnérables de l\'Est de la RD Congo.',
                    'en' => 'Ensure food security for farming households and vulnerable populations in eastern DR Congo.',
                ],
                'activities' => [
                    'fr' => 'Renforcement technique et accompagnement des ménages agricoles ; assistance alimentaire des ménages déplacés, retournés et familles d\'accueil ; distribution de kits agricoles d\'urgence (semences, outils aratoires) ; vulgarisation des méthodes culturales ; drainage de marais ; accès juste et équitable à la terre ; cohabitation pacifique ; appui en AGR.',
                    'en' => 'Technical capacity building and support for farming households; food assistance for displaced households, returnees, and host families; distribution of emergency agricultural kits (seeds, farming tools); dissemination of farming techniques; swamp drainage; fair and equitable access to land; peaceful coexistence; support for income-generating activities.',
                ],
                'icon' => 'wheat-awn',
                'intro' => [
                    'fr' => 'Kits agricoles, drainage de marais, appui aux ménages déplacés et activités génératrices de revenus.',
                    'en' => 'Agricultural kits, swamp drainage, support to displaced households, and income-generating activities.',
                ],
                'interventions' => [
                    'fr' => [
                        'Renforcement technique et accompagnement des ménages agricoles',
                        'Assistance alimentaire aux ménages déplacés, retournés et aux familles d’accueil',
                        'Distribution de kits agricoles d’urgence (semences et outils aratoires)',
                        'Vulgarisation des méthodes culturales et encadrement des agriculteurs',
                        'Drainage de marais',
                        'Gestion et accès à la terre justes et équitables',
                        'Cohabitation pacifique dans nos rayons d’action',
                        'Appui aux activités génératrices de revenus (AGR)',
                    ],
                    'en' => [
                        'Technical support and accompaniment of farming households',
                        'Food assistance to displaced and returnee households and host families',
                        'Distribution of emergency agricultural kits (seeds and hand tools)',
                        'Promotion of farming practices and farmer coaching',
                        'Swamp drainage',
                        'Fair and equitable land management and access',
                        'Peaceful coexistence in our areas of operation',
                        'Support to income-generating activities (IGA)',
                    ],
                ],
                'formations' => [
                    'fr' => [
                        'Collecte des données de prix des denrées alimentaires, avec le PAM et le ministère de l’Agriculture',
                    ],
                    'en' => [
                        'Collection of food commodity price data, with WFP and the Ministry of Agriculture',
                    ],
                ],
                'position' => 2,
            ],
            [
                'slug' => 'logistique',
                'name' => ['fr' => 'Logistique', 'en' => 'Logistics'],
                'objectives' => [
                    'fr' => 'Assurer la logistique des activités de l\'ONG.',
                    'en' => 'Ensure the logistics of the NGO\'s activities.',
                ],
                'activities' => [
                    'fr' => 'Réhabilitation des infrastructures de base ; aménagement des routes de desserte agricole (ponts, passages sous route, dalots) ; création et curage des canalisations et saignées ; construction/réhabilitation des infrastructures publiques et sociales.',
                    'en' => 'Rehabilitation of basic infrastructure; development of agricultural access roads (bridges, underpasses, culverts); creation and cleaning of channels and drains; construction/rehabilitation of public and social infrastructure.',
                ],
                'icon' => 'truck',
                'intro' => [
                    'fr' => 'Routes de desserte agricole, ponts, dalots et infrastructures publiques et sociales.',
                    'en' => 'Farm access roads, bridges, culverts, and public and social infrastructure.',
                ],
                'interventions' => [
                    'fr' => [
                        'Réhabilitation des infrastructures de base',
                        'Aménagement des routes de desserte agricole : ponts, passages sous route, dalots',
                        'Création et curage de canalisations et de saignées',
                        'Construction et réhabilitation d’infrastructures publiques et sociales',
                    ],
                    'en' => [
                        'Rehabilitation of basic infrastructure',
                        'Development of farm access roads: bridges, underpasses, culverts',
                        'Creation and cleaning of channels and drains',
                        'Construction and rehabilitation of public and social infrastructure',
                    ],
                ],
                'formations' => [
                    'fr' => [],
                    'en' => [],
                ],
                'position' => 3,
            ],
            [
                'slug' => 'education',
                'name' => ['fr' => 'Éducation', 'en' => 'Education'],
                'objectives' => [
                    'fr' => 'Améliorer l\'accès à l\'éducation des populations de l\'Est de la RD Congo.',
                    'en' => 'Improve access to education for populations in eastern DR Congo.',
                ],
                'activities' => [
                    'fr' => 'Construction et réhabilitation des infrastructures scolaires ; fourniture d\'équipements et de kits scolaires ; formation des enseignants.',
                    'en' => 'Construction and rehabilitation of school infrastructure; provision of equipment and school kits;',
                ],
                'icon' => 'graduation-cap',
                'intro' => [
                    'fr' => 'Construction d’écoles, équipements, kits scolaires et formation des enseignants.',
                    'en' => 'Construction of schools, equipment, school kits, and teacher training.',
                ],
                'interventions' => [
                    'fr' => [
                        'Construction et réhabilitation d’infrastructures scolaires',
                        'Fourniture d’équipements et de kits scolaires',
                        'Formation des enseignants',
                    ],
                    'en' => [
                        'Construction and rehabilitation of school infrastructure',
                        'Supply of school equipment and kits',
                        'Teacher training',
                    ],
                ],
                'formations' => [
                    'fr' => [
                        'Normes INEE : éducation en situation d’urgence (atelier de formation)',
                    ],
                    'en' => [
                        'INEE standards: education in emergencies (training workshop)',
                    ],
                ],
                'position' => 4,
            ],
            [
                'slug' => 'wash',
                'name' => ['fr' => 'Eau, hygiène et assainissement', 'en' => 'WASH'],
                'objectives' => [
                    'fr' => 'Améliorer les conditions d\'hygiène et d\'assainissement dans les communautés ciblées.',
                    'en' => 'Improve hygiene and sanitation conditions in target communities.',
                ],
                'activities' => [
                    'fr' => 'Étude et conception des projets hydrauliques ; adduction d\'eau ; captage de sources ; forages de puits ; construction de latrines publiques et familiales ; sensibilisation à l\'eau, l\'hygiène et l\'assainissement.',
                    'en' => 'Study and design of hydraulic projects; water supply; spring capture; well drilling; construction of public and family latrines; awareness on water, hygiene, and sanitation.',
                ],
                'icon' => 'droplet',
                'intro' => [
                    'fr' => 'Adduction d’eau, captage de sources, forages, latrines et sensibilisation à l’hygiène.',
                    'en' => 'Drinking water supply, spring capture, boreholes, latrines, and hygiene awareness.',
                ],
                'interventions' => [
                    'fr' => [
                        'Étude et conception de projets hydrauliques',
                        'Adduction d’eau et captage de sources',
                        'Forage de puits',
                        'Construction de latrines publiques et familiales',
                        'Sensibilisation à l’eau, à l’hygiène et à l’assainissement',
                    ],
                    'en' => [
                        'Design and study of water projects',
                        'Drinking water supply and spring capture',
                        'Well drilling',
                        'Construction of public and household latrines',
                        'Sensitisation on water, hygiene, and sanitation',
                    ],
                ],
                'formations' => [
                    'fr' => [
                        'Atelier de l’UNICEF sur les 5 engagements du cluster WASH pour la sécurité et la dignité des filles, garçons, femmes et hommes',
                    ],
                    'en' => [
                        'UNICEF workshop on the 5 WASH cluster engagements for the safety and dignity of girls, boys, women, and men',
                    ],
                ],
                'position' => 5,
            ],
            [
                'slug' => 'genre',
                'name' => ['fr' => 'Genre', 'en' => 'Gender'],
                'objectives' => [
                    'fr' => 'Promouvoir l\'égalité des sexes et l\'autonomisation des femmes dans les communautés ciblées.',
                    'en' => 'Promote gender equality and women\'s empowerment in target communities.',
                ],
                'activities' => [
                    'fr' => 'Sensibilisation sur l\'intégration du genre et le respect de l\'équité et de l\'égalité de sexe et de chance pour homme, femme, fille et garçon dans tous les secteurs d\'intervention.',
                    'en' => 'Awareness on gender integration and respect for equity and equality of sex and opportunity for men, women, girls, and boys in all sectors of intervention.',
                ],
                'icon' => 'venus',
                'intro' => [
                    'fr' => 'Équité, égalité de sexe et de chances pour les femmes, hommes, filles et garçons.',
                    'en' => 'Equity, equality of sex and opportunity for women, men, girls, and boys.',
                ],
                'interventions' => [
                    'fr' => [
                        'Sensibilisation à l’intégration du genre et au respect de l’équité',
                        'Égalité de sexe et de chances pour hommes, femmes, filles et garçons dans tous les secteurs',
                    ],
                    'en' => [
                        'Awareness on gender mainstreaming and respect for equity',
                        'Equality of sex and opportunity for men, women, girls, and boys in all sectors',
                    ],
                ],
                'formations' => [
                    'fr' => [
                        'Formation en genre par le HCR, à Bukavu',
                        'Outils d’intégration du genre : analyse de genre, cadre ADAPT, AGIR et Gender Marker',
                        'Premier atelier d’appui aux points focaux genre au Sud-Kivu, avec ICCO et OCHA',
                    ],
                    'en' => [
                        'Gender training by UNHCR, in Bukavu',
                        'Gender mainstreaming tools: gender analysis, ADAPT framework, AGIR, and Gender Marker',
                        'First support workshop for gender focal points in South Kivu, with ICCO and OCHA',
                    ],
                ],
                'position' => 6,
            ],
            [
                'slug' => 'protection',
                'name' => ['fr' => 'Protection', 'en' => 'Protection'],
                'objectives' => [
                    'fr' => 'Assurer la protection des populations vulnérables dans les communautés ciblées.',
                    'en' => 'Ensure the protection of vulnerable populations in target communities.',
                ],
                'activities' => [
                    'fr' => 'Promotion des droits de la femme et de l\'enfant ; bonne gouvernance ; lutte contre les viols, violences sexuelles et VBG ; lutte contre les IST/VIH-SIDA ; réinsertion socio-économique des personnes vulnérables, déplacées, retournées et vivant avec handicap.',
                    'en' => 'Promotion of women\'s and children\'s rights; good governance ; fight against rape, sexual violence, and GBV; fight against STIs/HIV/AIDS; socio-economic reintegration of vulnerable people, displaced persons, returnees, and people living with disabilities.',
                ],
                'icon' => 'shield-halved',
                'intro' => [
                    'fr' => 'Droits de la femme et de l’enfant, lutte contre les violences basées sur le genre, réinsertion des personnes vulnérables.',
                    'en' => 'Women’s and children’s rights, fight against gender-based violence, reintegration of vulnerable people.',
                ],
                'interventions' => [
                    'fr' => [
                        'Promotion des droits de la femme et de l’enfant',
                        'Bonne gouvernance',
                        'Lutte contre les viols et violences sexuelles et les violences basées sur le genre',
                        'Lutte contre les IST et le VIH/SIDA',
                        'Réinsertion socio-économique des personnes vulnérables, déplacées et retournées',
                        'Réinsertion des personnes vivant avec un handicap',
                    ],
                    'en' => [
                        'Promotion of women’s and children’s rights',
                        'Good governance',
                        'Fight against rape, sexual violence, and gender-based violence',
                        'Fight against STIs and HIV/AIDS',
                        'Socio-economic reintegration of vulnerable, displaced, and returnee people',
                        'Reintegration of people living with disabilities',
                    ],
                ],
                'formations' => [
                    'fr' => [
                        'Management sensible aux conflits',
                        'Aide humanitaire d’urgence et sensibilité aux conflits',
                    ],
                    'en' => [
                        'Conflict-sensitive management',
                        'Emergency humanitarian aid and conflict sensitivity',
                    ],
                ],
                'position' => 7,
            ],
            [
                'slug' => 'environnement',
                'name' => ['fr' => 'Environnement', 'en' => 'Environment'],
                'objectives' => [
                    'fr' => 'Promouvoir la protection de l\'environnement et la gestion durable des ressources naturelles dans les communautés ciblées.',
                    'en' => 'Promote environmental protection and sustainable management of natural resources in target communities.',
                ],
                'activities' => [
                    'fr' => 'Sensibilisation au droit de l\'environnement et à la résolution 1325 de l\'ONU ; protection des forêts et de la biodiversité ; lutte contre le changement climatique ; promotion des énergies renouvelables ; reboisements ; fabrication de briquettes et pavés à partir des déchets ménagers ; biodigesteurs et engrais biologique.',
                    'en' => 'Awareness on environmental law and UN Resolution 1325; forest and biodiversity protection; fight against climate change; promotion of renewable energy; reforestation; production of briquettes and paving stones from household waste; biodigesters and organic fertilizers.',
                ],
                'icon' => 'leaf',
                'intro' => [
                    'fr' => 'Reboisement, énergies renouvelables, biodigesteurs, briquettes issues de déchets ménagers.',
                    'en' => 'Reforestation, renewable energy, biodigesters, and briquettes made from household waste.',
                ],
                'interventions' => [
                    'fr' => [
                        'Sensibilisation au droit de l’environnement et à la résolution 1325 de l’ONU',
                        'Protection de l’environnement, des forêts et de la biodiversité',
                        'Lutte contre le changement climatique',
                        'Promotion des énergies renouvelables et alternatives',
                        'Reboisement',
                        'Briquettes à partir de déchets ménagers et fabrication de pavés',
                        'Biodigesteurs et production d’engrais biologique',
                    ],
                    'en' => [
                        'Awareness on environmental law and UN Resolution 1325',
                        'Protection of the environment, forests, and biodiversity',
                        'Fight against climate change',
                        'Promotion of renewable and alternative energies',
                        'Reforestation',
                        'Briquettes from household waste and production of paving stones',
                        'Biodigesters and production of organic fertiliser',
                    ],
                ],
                'formations' => [
                    'fr' => [
                        'Forum sur la séquestration du carbone et le changement climatique, à Kigali (Rwanda), 2016 — IPBS, NAMA Facility, UNFCCC',
                    ],
                    'en' => [
                        'Forum on carbon sequestration and climate change, in Kigali (Rwanda), 2016 — IPBS, NAMA Facility, UNFCCC',
                    ],
                ],
                'position' => 8,
            ],
            [
                'slug' => 'formation-professionnelle',
                'name' => ['fr' => 'Formation professionnelle', 'en' => 'Professional Training'],
                'objectives' => [
                    'fr' => 'Favoriser l\'accès à la formation professionnelle pour les populations ciblées.',
                    'en' => 'Promote access to professional training for target populations.',
                ],
                'activities' => [
                    'fr' => 'Formation en coupe-couture, maçonnerie, menuiserie, briqueterie, savonnerie et art culinaire.',
                    'en' => 'Training in sewing, masonry, carpentry, brickmaking, soap making, and culinary arts.',
                ],
                'icon' => 'screwdriver-wrench',
                'intro' => [
                    'fr' => 'Couture, maçonnerie, menuiserie, briqueterie, savonnerie et art culinaire.',
                    'en' => 'Sewing, masonry, carpentry, brick-making, soap-making, and culinary arts.',
                ],
                'interventions' => [
                    'fr' => [
                        'Coupe-couture',
                        'Maçonnerie',
                        'Menuiserie',
                        'Briqueterie',
                        'Savonnerie',
                        'Art culinaire',
                    ],
                    'en' => [
                        'Tailoring and sewing',
                        'Masonry',
                        'Carpentry',
                        'Brick-making',
                        'Soap-making',
                        'Culinary arts',
                    ],
                ],
                'formations' => [
                    'fr' => [],
                    'en' => [],
                ],
                'position' => 9,
            ],

        ];

        foreach ($domains as $domain) {
            Domain::updateOrCreate(['slug' => $domain['slug']], $domain);
        }
    }
}
