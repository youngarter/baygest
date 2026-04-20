<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resolution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->restrictOnDelete();
            $table->enum('decision', ['for', 'against', 'abstain']);
            // Snapshot du tantiemes au moment du vote (historisation)
            $table->decimal('weight_used', 10, 4)->default(1);
            $table->timestamps();

            $table->unique(['resolution_id', 'unit_id']);
            $table->index('resolution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
