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
    |  - `icons` : chemins SVG (24×24, trait fin) des pictogrammes couleur
    |    « lac » du rail et des cartes, indexés par slug.
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
        'icons' => [
            'sante'                     => 'M12 8v8M8 12h8M12 3a9 9 0 100 18 9 9 0 000-18z',
            'securite-alimentaire'      => 'M12 21V9M12 13c-4 0-6-2-6-6 4 0 6 2 6 6zM12 11c0-4 2-6 6-6 0 4-2 6-6 6z',
            'logistique'                => 'M3 18h18M5 18v-6a7 7 0 0114 0v6M12 5v13',
            'education'                 => 'M3 5h6a3 3 0 013 3v12a2 2 0 00-2-2H3zM21 5h-6a3 3 0 00-3 3v12a2 2 0 012-2h7z',
            'wash'                      => 'M12 3s6 6.5 6 11a6 6 0 01-12 0c0-4.5 6-11 6-11z',
            'genre'                     => 'M12 14a5 5 0 100-10 5 5 0 000 10zM12 14v7M9 18h6',
            'protection'                => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z',
            'environnement'             => 'M5 19c0-9 5-14 15-14 0 10-5 15-14 15M5 19c2-5 5-8 9-10',
            'formation-professionnelle' => 'M14 6l4 4M4 20l8-8M13 5l6 6-3 3-6-6z',
        ],
    ],
];
