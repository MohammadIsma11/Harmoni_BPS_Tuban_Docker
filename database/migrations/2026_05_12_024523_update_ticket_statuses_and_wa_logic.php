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
        // Postgres change column type for enum requires raw statement
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ALTER COLUMN status DROP DEFAULT");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ALTER COLUMN status TYPE VARCHAR(255)");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ALTER COLUMN status SET DEFAULT 'open'");
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ALTER COLUMN wa_status DROP DEFAULT");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ALTER COLUMN wa_status TYPE VARCHAR(255)");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ALTER COLUMN wa_status SET DEFAULT 'None'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
