<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('residence_id')->unique()->constrained()->cascadeOnDelete();

            // Nullable: renseignés par InitializeResidenceAccounting
            $table->foreignId('default_bank_account_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();
            $table->foreignId('default_vendor_account_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();
            $table->foreignId('default_owner_account_id')
                ->nullable()
                ->constrained('chart_of_accounts')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_configs');
    }
};
