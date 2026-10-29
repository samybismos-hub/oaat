<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Les albums reçoivent un identifiant lisible (slug), comme les projets ou
     * les domaines : c'est lui que la route publique « Galerie » utilise dans
     * son URL (/galerie/{slug}). Jusqu'ici la colonne n'existait pas, si bien
     * que GalerieController@show déclenchait une erreur SQL.
     */
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            // Nullable au départ : la colonne est remplie juste après.
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Rétro-remplissage des albums existants (ex. l'album de démonstration
        // du seeder), à partir de leur titre français.
        DB::table('albums')->orderBy('id')->get()->each(function (object $album): void {
            $titreFr = data_get(json_decode((string) $album->title, true) ?? [], 'fr');

            if (is_string($titreFr) && trim($titreFr) !== '') {
                DB::table('albums')
                    ->where('id', $album->id)
                    ->update(['slug' => Str::slug($titreFr)]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
