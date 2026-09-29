<?php

namespace App\Models;

use App\Enums\PartnerRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * La ligne de la table pivot `project_partner`.
 *
 * Le « rôle » n'appartient ni au projet, ni au partenaire : il n'existe que
 * dans le couple (projet + partenaire). Un même partenaire peut donc être
 * bailleur sur un projet et simple exécutant sur un autre.
 *
 * Sans cette classe, `$partner->pivot->role` renverrait la chaîne « funder ».
 * Avec elle, Eloquent renvoie directement une valeur de l'enum PartnerRole :
 * libellés et couleurs sont alors utilisables partout dans l'administration.
 *
 * Bon à savoir : Laravel décide lui-même d'activer ou non les timestamps du
 * pivot selon que la requête a ramené la colonne created_at (voir
 * AsPivot::fromAttributes) ; nous ne forçons donc rien ici.
 */
class ProjectPartner extends Pivot
{
    protected $table = 'project_partner';

    protected function casts(): array
    {
        return [
            'role' => PartnerRole::class,
        ];
    }
}
