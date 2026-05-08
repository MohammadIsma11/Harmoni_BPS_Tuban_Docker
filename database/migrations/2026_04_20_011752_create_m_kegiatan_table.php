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
        Schema::create('m_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan', 150);
            
            // tim_id (BigInt, FK): Referens ke tabel teams
            $table->foreignId('tim_id')->constrained('teams')->onDelete('cascade');
            
            $table->decimal('min_honor_standard', 15, 2)->default(0);
            $table->decimal('max_honor_standard', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_kegiatan');
    }
};
