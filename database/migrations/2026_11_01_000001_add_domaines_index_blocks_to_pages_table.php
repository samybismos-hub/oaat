<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Colonnes de contenu de la page CMS "domaines" (vue publics/domaines-index).
 *
 * Le bloc "Compétences transversales" et le bandeau d'appel à l'action étaient
 * en dur dans la vue : ils deviennent éditables via les Pages du site (Filament)
 * sous forme de colonnes JSON traduisibles, comme `timeline`/`zones` pour la
 * page "organisation".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('competences_titre')->nullable()->after('zones');
            $table->json('competences_intro')->nullable()->after('competences_titre');
            $table->json('competences')->nullable()->after('competences_intro');
            $table->json('cta_titre')->nullable()->after('competences');
            $table->json('cta_intro')->nullable()->after('cta_titre');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['competences_titre', 'competences_intro', 'competences', 'cta_titre', 'cta_intro']);
        });
    }
};