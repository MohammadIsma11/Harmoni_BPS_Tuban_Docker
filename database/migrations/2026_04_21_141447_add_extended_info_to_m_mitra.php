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
            $table->string('posisi', 100)->nullable()->after('nama_lengkap');
            $table->string('status_seleksi', 50)->nullable()->after('posisi');
            $table->string('posisi_daftar', 255)->nullable()->after('status_seleksi');
            $table->string('tempat_lahir', 100)->nullable()->after('alamat_detail');
            $table->date('tgl_lahir')->nullable()->after('tempat_lahir');
            $table->integer('umur')->nullable()->after('tgl_lahir');
            $table->string('pendidikan', 100)->nullable()->after('umur');
            $table->string('pekerjaan', 150)->nullable()->after('pendidikan');
            $table->text('deskripsi_pekerjaan_lain')->nullable()->after('pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_mitra', function (Blueprint $table) {
            $table->dropColumn([
                'posisi',
                'status_seleksi',
                'posisi_daftar',
                'tempat_lahir',
                'tgl_lahir',
                'umur',
                'pendidikan',
                'pekerjaan',
                'deskripsi_pekerjaan_lain'
            ]);
        });
    }
};
