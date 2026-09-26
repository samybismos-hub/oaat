<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Ordre d'exécution important :
     * - les partenaires doivent exister avant les projets (table pivot project_partner) ;
     * - les domaines doivent exister avant les projets (clé étrangère domain_id).
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            PageSeeder::class,
            TeamMemberSeeder::class,
            PartnerSeeder::class,
            DomainSeeder::class,
            ProjectSeeder::class,
            ActualitySeeder::class,
            DocumentSeeder::class,
            AlbumSeeder::class,
        ]);
    }
}
