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
        Schema::table('tematiks', function (Blueprint $table) {
            $table->string('desa', 100)->nullable()->after('kecamatan');
            $table->string('sls', 200)->nullable()->after('desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tematiks', function (Blueprint $table) {
            $table->dropColumn(['desa', 'sls']);
        });
    }
};
