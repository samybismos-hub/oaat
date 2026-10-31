<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Colonnes éditoriales des domaines d'intervention, alimentées par la
     * page publique « Domaines » (maquette) :
     *  - intro         : phrase d'accroche sous le nom du domaine (le
     *    « descriptif » de la maquette, désormais une vraie donnée) ;
     *  - interventions : liste des actions menées par l'OAAT dans le domaine ;
     *  - formations    : liste des formations reçues des partenaires.
     *
     * JSON bilingue ({fr,en}) comme les autres colonnes traduites du domaine.
     * Les valeurs sont fournies par le DomainSeeder (updateOrCreate -> backfill).
     */
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->json('intro')->nullable()->after('name');
            $table->json('interventions')->nullable()->after('activities');
            $table->json('formations')->nullable()->after('interventions');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['intro', 'interventions', 'formations']);
        });
    }
};
