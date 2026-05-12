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
        // Drop the leftover constraint if it exists
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets DROP CONSTRAINT IF EXISTS tickets_status_new_check");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets DROP CONSTRAINT IF EXISTS tickets_status_check");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status_new')->nullable();
        });
    }
};
