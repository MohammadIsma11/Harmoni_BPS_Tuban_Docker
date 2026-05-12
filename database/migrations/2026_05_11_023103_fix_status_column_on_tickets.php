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
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('status', 'status_new');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'diproses', 'selesai', 'ditutup'])->default('open');
        });
    }
};
