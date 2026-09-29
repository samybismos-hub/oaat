<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Le rôle d'un partenaire... mais attention : ce rôle n'appartient pas au
 * partenaire lui-même, il appartient au COUPLE (projet + partenaire).
 *
 * Un même partenaire peut être bailleur sur un projet et simple exécutant
 * sur un autre : c'est pourquoi la valeur est stockée dans la table pivot
 * `project_partner`, et non dans la table `partners`.
 *
 * La base n'accepte que ces deux valeurs (colonne ENUM) et elles peuvent
 * rester vides : un partenariat dont le rôle n'est pas encore documenté est
 * un cas réel chez l'OAAT (voir les mentions « à préciser » du PartnerSeeder).
 */
enum PartnerRole: string implements HasColor, HasLabel
{
    case Funder = 'funder';
    case Implementer = 'implementer';

    public function getLabel(): string
    {
        return match ($this) {
            self::Funder => 'Bailleur / Financeur',
            self::Implementer => 'Partenaire de mise en œuvre',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Funder => 'success',
            self::Implementer => 'info',
        };
    }
}
