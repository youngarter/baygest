<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('residence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('budget_line_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->decimal('amount_total', 12, 2);
            $table->enum('status', ['draft', 'validated', 'paid'])->default('draft');
            $table->timestamps();

            $table->index(['residence_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
