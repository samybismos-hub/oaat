<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Colonne de contenu de la page CMS "domaines" (vue publique domaines-index).
 *
 * Conformément au principe des pages du site, seul le contenu réellement
 * éditable est stocké ici : la liste des compétences transversales (colonne
 * JSON traduisible, comme `timeline`/`zones` pour la page "organisation").
 * Les titres et introductions de bandeaux restent codés en dur dans la vue.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('competences')->nullable()->after('zones');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('competences');
        });
    }
};