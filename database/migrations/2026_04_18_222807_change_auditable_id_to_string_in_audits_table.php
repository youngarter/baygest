<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex('audits_auditable_type_auditable_id_index');
            $table->string('auditable_id', 255)->change();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex('audits_auditable_type_auditable_id_index');
            $table->unsignedBigInteger('auditable_id')->change();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }
};
