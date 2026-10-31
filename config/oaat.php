<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Données institutionnelles statiques
    |--------------------------------------------------------------------------
    |
    | Ces valeurs ne changent pas : elles n'ont donc pas leur place en base
    | de données (pas de colonne, pas de champ à remplir dans l'admin).
    |
    */

    // Date de création de l'organisation (source : presentation OAAT, I. Identification).
    'founded_at' => '1995-05-10',

    /*
    | Compteurs des blocs « Expérience comparable » et « Autres projets »
    | de la fiche projet (pages.projets-show). Configurables un jour côté
    | administration, sans toucher au template.
    */
    'projects' => [
        'experience_count' => 4,
        'related_count'    => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page « Domaines d'intervention » (publiée)
    |--------------------------------------------------------------------------
    |
    | Données de présentation (design figé — décision du 31/10/2026, option B) :
    |  - `tints` : teinte HSL de chaque domaine, indexée par slug de la table
    |    `domains`. Sert aux dégradés d'illustration de la page domaines-index.
    |  - `rail`  : libellés courts de la barre de navigation interne, indexés
    |    par slug. (ex. « Alimentation » vs « Sécurité alimentaire »).
    |
    | Ces valeurs relèvent du style et ne changent pas : elles n'ont PAS leur
    | place en base de données. Les contenus éditoriaux, eux (interventions,
    | formations), vivent dans `domains` (étape 2).
    |
    */
    'domaine_page' => [
        'tints' => [
            'sante'                     => 350,
            'securite-alimentaire'      => 95,
            'logistique'                => 30,
            'education'                 => 215,
            'wash'                      => 190,
            'genre'                     => 285,
            'protection'                => 330,
            'environnement'             => 150,
            'formation-professionnelle' => 45,
        ],
        'rail' => [
            'sante'                     => 'Santé',
            'securite-alimentaire'      => 'Alimentation',
            'logistique'                => 'Logistique',
            'education'                 => 'Éducation',
            'wash'                      => 'Eau',
            'genre'                     => 'Genre',
            'protection'                => 'Protection',
            'environnement'             => 'Environnement',
            'formation-professionnelle' => 'Métiers',
        ],
    ],
];
