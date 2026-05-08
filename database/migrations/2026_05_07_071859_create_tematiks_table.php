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
        Schema::create('tematiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('pic', 100);
            $table->text('member')->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('judul', 200);
            $table->date('tanggal');
            $table->string('status', 50)->default('Active');
            $table->double('lat');
            $table->double('lng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tematiks');
    }
};
