<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Les états possibles d'un projet de l'OAAT.
 *
 * La base de données n'accepte que ces quatre valeurs (colonne ENUM) :
 * cet enum en est la traduction côté PHP. Comme il implémente les contrats
 * HasLabel et HasColor, Filament sait tout seul :
 *   - afficher « En cours » au lieu de « ongoing » dans les menus déroulants ;
 *   - colorer les pastilles de statut dans les tableaux.
 */
enum ProjectStatus: string implements HasColor, HasLabel
{
    case Planned = 'planned';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case AwaitingFunding = 'awaiting_funding';

    public function getLabel(): string
    {
        return match ($this) {
            self::Planned => 'Planifié',
            self::Ongoing => 'En cours',
            self::Completed => 'Réalisé',
            self::AwaitingFunding => 'En attente de financement',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Planned => 'gray',
            self::Ongoing => 'warning',
            self::Completed => 'success',
            self::AwaitingFunding => 'danger',
        };
    }
}
