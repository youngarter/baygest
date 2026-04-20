<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('audit_logs');
    }

    public function down(): void
    {
        // audit_logs was a redundant custom audit table replaced by OwenIt\Auditing
    }
};
