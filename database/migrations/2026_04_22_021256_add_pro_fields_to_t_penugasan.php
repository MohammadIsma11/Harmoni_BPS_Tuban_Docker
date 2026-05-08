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
        Schema::table('t_penugasan', function (Blueprint $table) {
            $table->string('no_spk', 100)->nullable()->after('total_honor_tugas');
            $table->string('no_bast', 100)->nullable()->after('no_spk');
            $table->string('no_spp', 100)->nullable()->after('no_bast');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_penugasan', function (Blueprint $table) {
            //
        });
    }
};
