<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            // assemblees.id is char(36) — match the UUID type
            $table->char('assemblee_id', 36);
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('resolution_type', [
                'budget_approval',
                'work_approval',
                'contract_approval',
                'other',
            ]);
            $table->timestamps();

            $table->foreign('assemblee_id')->references('id')->on('assemblees')->cascadeOnDelete();
            $table->index('assemblee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolutions');
    }
};
