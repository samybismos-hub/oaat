<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->json('organisation_name')->nullable();
            $table->string('email')->nullable();
            $table->json('phones')->nullable();
            $table->json('addresses')->nullable();
            $table->json('socials')->nullable();
            $table->unsignedInteger('stat_projects')->nullable();
            $table->unsignedInteger('stat_beneficiaries')->nullable();
            $table->unsignedInteger('stat_zones')->nullable();
            $table->unsignedInteger('stat_years')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
