<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nettoyage du schéma "pages" : supprime les colonnes expérimentales de la
 * page "domaines" (titres/intros de bandeaux) qui ne doivent PAS être
 * éditables depuis l'administration. Conformément au principe des pages du
 * site, seul le contenu réellement éditable (`competences`) est exposé à
 * Filament ; les libellés de structure restent codés en dur dans la vue.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['competences_titre', 'competences_intro', 'cta_titre', 'cta_intro'] as $column) {
            if (Schema::hasColumn('pages', $column)) {
                Schema::table('pages', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('competences_titre')->nullable()->after('zones');
            $table->json('competences_intro')->nullable()->after('competences_titre');
            $table->json('cta_titre')->nullable()->after('competences');
            $table->json('cta_intro')->nullable()->after('cta_titre');
        });
    }
};