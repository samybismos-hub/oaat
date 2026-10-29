<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mention de crédit photo de la fiche projet, saisie dans l'administration
     * (ProjectForm → section « Crédit photo ») et affichée sous la photo de
     * couverture et sous chaque photo de la galerie.
     *
     * JSON bilingue ({fr, en}) comme les autres colonnes traduites du projet.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('credit_photo')->nullable()->after('composantes');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('credit_photo');
        });
    }
};
