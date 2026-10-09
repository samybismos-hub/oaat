<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->json('bio')->nullable()->after('role');
            $table->json('quote')->nullable()->after('bio');
            $table->boolean('is_founder')->default(false)->after('quote');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['bio', 'quote', 'is_founder']);
        });
    }
};