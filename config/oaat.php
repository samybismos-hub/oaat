<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DonnÃ©es institutionnelles statiques
    |--------------------------------------------------------------------------
    |
    | Ces valeurs ne changent pas : elles n'ont donc pas leur place en base
    | de donnÃ©es (pas de colonne, pas de champ Ã  remplir dans l'admin).
    |
    */

    'founded_at' => '1995-05-10',

    'projects' => [
        'experience_count' => 4,
        'related_count'    => 3,
    ],

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
            'sante'                     => 'SantÃ©',
            'securite-alimentaire'      => 'Alimentation',
            'logistique'                => 'Logistique',
            'education'                 => 'Ã‰ducation',
            'wash'                      => 'Eau',
            'genre'                     => 'Genre',
            'protection'                => 'Protection',
            'environnement'             => 'Environnement',
            'formation-professionnelle' => 'MÃ©tiers',
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

    /*
    |--------------------------------------------------------------------------
    | Galerie — palette des collections (bandeau #cs)
    |--------------------------------------------------------------------------
    */
    'galerie' => [
        'collection_palette' => [205, 350, 95, 30, 215, 190, 285, 330, 150, 45],
    ],
];
