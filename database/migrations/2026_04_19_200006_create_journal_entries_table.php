<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('residence_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('reference');
            $table->string('description')->nullable();
            $table->nullableMorphs('source');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['residence_id', 'reference']);
            $table->index(['residence_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
