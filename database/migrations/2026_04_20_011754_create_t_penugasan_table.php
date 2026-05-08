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
        Schema::create('t_penugasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('m_kegiatan')->onDelete('cascade');
            
            // mitra_id (Varchar, FK): Referensi ke sobat_id di tabel m_mitra.
            $table->string('mitra_id', 50);
            $table->foreign('mitra_id')->references('sobat_id')->on('m_mitra')->onDelete('cascade');
            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Katim
            
            $table->decimal('total_honor_tugas', 15, 2);
            $table->date('tgl_selesai_target');
            
            $table->enum('status_tugas', ['Progres', 'Selesai'])->default('Progres');
            $table->enum('status_dokumen', ['Pending', 'Lengkap', 'Revisi'])->default('Pending');
            
            $table->string('file_pendukung')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_penugasan');
    }
};
