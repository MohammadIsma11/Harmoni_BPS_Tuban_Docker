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
        Schema::create('t_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->constrained('t_penugasan')->onDelete('cascade');
            $table->decimal('nominal_cair', 15, 2);
            $table->string('bulan_bayar', 7); // Format: YYYY-MM
            
            $table->enum('status_bayar', ['Lunas', 'Antre'])->default('Antre');
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_pembayaran');
    }
};
