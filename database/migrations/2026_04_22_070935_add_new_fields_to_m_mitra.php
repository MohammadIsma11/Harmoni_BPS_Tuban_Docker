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
        Schema::table('m_mitra', function (Blueprint $table) {
            $table->string('alamat_prov')->nullable()->after('alamat_detail');
            $table->string('alamat_kab')->nullable()->after('alamat_prov');
            $table->string('alamat_kec')->nullable()->after('alamat_kab');
            $table->string('alamat_desa')->nullable()->after('alamat_kec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_mitra', function (Blueprint $table) {
            $table->dropColumn(['alamat_prov', 'alamat_kab', 'alamat_kec', 'alamat_desa']);
        });
    }
};
