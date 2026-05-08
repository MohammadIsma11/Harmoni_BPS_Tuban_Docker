<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename table m_kegiatan to m_anggaran
        Schema::rename('m_kegiatan', 'm_anggaran');

        // 2. Modify m_anggaran columns
        Schema::table('m_anggaran', function (Blueprint $table) {
            $table->string('kode_anggaran', 50)->nullable()->after('id');
            // nama_kegiatan can stay as the activity name or be repurposed as nama_pagu
            $table->renameColumn('nama_kegiatan', 'nama_pagu');
            $table->decimal('budget_tahunan', 15, 2)->default(0)->after('tim_id');
            
            // Remove old constraints if they exist
            if (Schema::hasColumn('m_anggaran', 'min_honor_standard')) {
                $table->dropColumn(['min_honor_standard', 'max_honor_standard']);
            }
        });

        // 3. Modify t_penugasan columns
        Schema::table('t_penugasan', function (Blueprint $table) {
            // Rename FK kegiatan_id to anggaran_id (logical rename, maintaining the reference)
            $table->renameColumn('kegiatan_id', 'anggaran_id');
            
            // Allow null for manual input fallback
            $table->unsignedBigInteger('anggaran_id')->nullable()->change();
            
            // New Detail Fields
            $table->string('nama_kegiatan_manual', 200)->nullable()->after('anggaran_id');
            $table->date('tgl_mulai')->nullable()->after('user_id');
            $table->string('satuan', 50)->nullable()->after('tgl_selesai_target');
            $table->decimal('volume', 10, 2)->default(1)->after('satuan');
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('volume');
            
            // Addition for Dashboard Categorization (Pendataan, Pemeriksaan, Pengolahan)
            $table->enum('kategori_kegiatan', ['Pendataan', 'Pemeriksaan', 'Pengolahan'])->default('Pendataan')->after('nama_kegiatan_manual');
        });
    }

    public function down(): void
    {
        Schema::table('t_penugasan', function (Blueprint $table) {
            $table->dropColumn(['nama_kegiatan_manual', 'kategori_kegiatan', 'tgl_mulai', 'satuan', 'volume', 'harga_satuan']);
            $table->renameColumn('anggaran_id', 'kegiatan_id');
        });

        Schema::table('m_anggaran', function (Blueprint $table) {
            $table->renameColumn('nama_pagu', 'nama_kegiatan');
            $table->dropColumn(['kode_anggaran', 'budget_tahunan']);
            $table->decimal('min_honor_standard', 15, 2)->default(0);
            $table->decimal('max_honor_standard', 15, 2)->default(0);
        });

        Schema::rename('m_anggaran', 'm_kegiatan');
    }
};
