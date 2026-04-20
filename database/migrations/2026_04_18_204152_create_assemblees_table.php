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
        Schema::create('assemblees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('residence_id')->constrained('residences')->cascadeOnDelete();
            $table->integer('annee_syndic');
            $table->enum('type', ['normal', 'extraordinaire'])->default('normal');
            $table->string('titre');
            $table->longText('description')->nullable();
            $table->date('date_assemblee');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['residence_id', 'annee_syndic']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assemblees');
    }
};
