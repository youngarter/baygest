<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            // budgets.id is char(36) — match the UUID type
            $table->char('budget_id', 36);
            $table->foreignId('chart_of_account_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->decimal('amount_previsionnel', 12, 2);
            $table->timestamps();

            $table->foreign('budget_id')->references('id')->on('budgets')->cascadeOnDelete();
            $table->index('budget_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
    }
};
