<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL: Drop existing check constraint and update enum
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            
            // Re-create the check constraint with 'Mitra' included
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('Admin', 'Kepala', 'Katim', 'Pegawai', 'Mitra'))");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['Admin', 'Kepala', 'Katim', 'Pegawai', 'Mitra'])->default('Pegawai')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('Admin', 'Kepala', 'Katim', 'Pegawai'))");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['Admin', 'Kepala', 'Katim', 'Pegawai'])->default('Pegawai')->change();
            });
        }
    }
};
