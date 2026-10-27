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
];
