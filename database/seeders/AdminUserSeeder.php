<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@oaat.test'], // Évite les doublons
            [
                'name' => 'Admin',
                'password' => Hash::make('Oaat2026'), // Assurez-vous de changer le mot de passe par défaut // À adapter selon votre logique de rôles
            ]
        );
    }
}
