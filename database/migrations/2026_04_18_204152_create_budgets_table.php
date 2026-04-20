<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('residence_id')->constrained('residences')->cascadeOnDelete();
            $table->uuid('assemblee_id');
            $table->foreign('assemblee_id')->references('id')->on('assemblees')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('type', ['global_estimatif', 'exceptionnel_estimatif'])->default('global_estimatif');
            $table->decimal('budget_reel', 12, 2)->default(0);
            $table->decimal('seuil_alerte_estimatif', 12, 2)->nullable();
            $table->decimal('seuil_alerte_reel', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['residence_id', 'assemblee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
