<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Contenu éditorial de la fiche projet (sections « Contexte »,
            // « Objectif général » + tableau des « Composantes »), saisi
            // depuis l'admin Filament au lieu d'être figé dans le template.
            $table->json('contexte')->nullable()->after('objectives');
            $table->json('objectif_general')->nullable()->after('contexte');
            $table->json('composantes')->nullable()->after('results');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['contexte', 'objectif_general', 'composantes']);
        });
    }
};
