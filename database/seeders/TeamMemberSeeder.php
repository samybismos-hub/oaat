<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    /**
     * Équipe / gouvernance. Seule personne nommée dans le PDF de présentation :
     * le Représentant national. L'organigramme complet doit être fourni par le client.
     */
    public function run(): void
    {
        $members = [
            [
                'name' => 'Ir MANEMA CIRHAHINGIRWA Roger',
                'role' => [
                    'fr' => 'Représentant national',
                    'en' => 'National Representative',
                ],
                'bio' => [
                    'fr' => 'Ingénieur, il fonde l\'organisation le 10 mai 1995 et la représente aujourd\'hui en qualité de Représentant national. Il est le responsable de l\'OAAT et l\'interlocuteur de ses partenaires.',
                    'en' => 'An engineer, he founded the organisation on 10 May 1995 and represents it today as National Representative. He heads OAAT and is the point of contact for its partners.',
                ],
                'quote' => [
                    'fr' => 'Ce n\'est pas normal qu\'il y ait toujours des gens pour demander et d\'autres pour donner.',
                    'en' => 'It is not normal that there are always people asking and others giving.',
                ],
                'is_founder' => true,
                'position' => 1,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], $member);
        }
    }
}
