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
                'position' => 4,
            ],
            [
                'slug' => 'wash',
                'name' => ['fr' => 'Hygiène et Assainissement', 'en' => 'WASH'],
                'objectives' => [
                    'fr' => 'Améliorer les conditions d\'hygiène et d\'assainissement dans les communautés ciblées.',
                    'en' => 'Improve hygiene and sanitation conditions in target communities.',
                ],
                'activities' => [
                    'fr' => 'Étude et conception des projets hydrauliques ; adduction d\'eau ; captage de sources ; forages de puits ; construction de latrines publiques et familiales ; sensibilisation à l\'eau, l\'hygiène et l\'assainissement.',
                    'en' => 'Study and design of hydraulic projects; water supply; spring capture; well drilling; construction of public and family latrines; awareness on water, hygiene, and sanitation.',
                ],
                'icon' => 'droplet',
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
                'position' => 9,
            ],

        ];

        foreach ($domains as $domain) {
            Domain::updateOrCreate(['slug' => $domain['slug']], $domain);
        }
    }
}
