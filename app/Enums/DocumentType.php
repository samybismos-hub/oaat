<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * La nature d'un document téléchargeable (colonne "type", de type chaîne).
 *
 * Comme ProjectStatus et PartnerRole, cet enum évite d'écrire à la main les
 * libellés (« rapport » → « Rapport d'activité ») et les couleurs de pastille.
 *
 * Les trois premières valeurs correspondent exactement à celles du
 * DocumentSeeder : les changer obligerait à réécrire le seeder.
 */
enum DocumentType: string implements HasColor, HasLabel
{
    case Report = 'rapport';
    case Certificate = 'attestation';
    case Study = 'etude';
    case Policy = 'politique';
    case Form = 'formulaire';
    case Other = 'autre';

    public function getLabel(): string
    {
        return match ($this) {
            self::Report => "Rapport d'activité ou d'évaluation",
            self::Certificate => 'Attestation & reconnaissance officielle',
            self::Study => 'Étude ou catalogue de projets',
            self::Policy => 'Politique, procédure ou statuts',
            self::Form => 'Formulaire à télécharger',
            self::Other => 'Autre document',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Report => 'info',
            self::Certificate => 'success',
            self::Study => 'warning',
            self::Policy => 'gray',
            self::Form => 'primary',
            self::Other => 'gray',
        };
    }
}
