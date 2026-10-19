<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pages', 'timeline')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->json('timeline')->nullable()->after('body');
                $table->json('recognitions')->nullable()->after('timeline');
                $table->json('zones')->nullable()->after('recognitions');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['timeline', 'recognitions', 'zones']);
        });
    }
};
