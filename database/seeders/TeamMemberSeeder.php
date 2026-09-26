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
                'position' => 1,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], $member);
        }
    }
}
