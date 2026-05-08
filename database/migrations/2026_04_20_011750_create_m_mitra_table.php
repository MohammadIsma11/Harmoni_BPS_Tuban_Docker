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
        Schema::create('m_mitra', function (Blueprint $table) {
            // sobat_id (Varchar, PK): ID unik mitra dari sistem SOBAT BPS.
            $table->string('sobat_id', 50)->primary();
            $table->string('nama_lengkap', 150);
            $table->string('email', 100)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat_detail')->nullable();
            
            // max_honor_bulanan (Decimal): Batas maksimal pendapatan mitra per bulan (Default: 2.500.000).
            $table->decimal('max_honor_bulanan', 15, 2)->default(2500000);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_mitra');
    }
};
