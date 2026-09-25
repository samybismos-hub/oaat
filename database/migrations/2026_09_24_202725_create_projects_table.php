<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->restrictOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->enum('status', ['planned', 'ongoing', 'completed'])->default('planned');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('objectives')->nullable();
            $table->json('beneficiaries')->nullable();
            $table->json('results')->nullable();
            $table->decimal('budget_amount', 15, 2)->nullable();
            $table->string('budget_currency', 3)->nullable();
            $table->unsignedInteger('beneficiaries_count')->nullable();
            $table->string('beneficiaries_unit')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
