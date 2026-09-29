<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les deux colonnes de référencement (SEO) à la table `pages`.
     *
     * Elles sont de type JSON, comme `title` et `body`, afin de stocker
     * une valeur différente par langue (clé "fr" et clé "en").
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('meta_title')->nullable()->after('body');
            $table->json('meta_description')->nullable()->after('meta_title');
        });
    }

    /**
     * Annule la migration (utile avec `php artisan migrate:rollback`).
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });
    }
};
