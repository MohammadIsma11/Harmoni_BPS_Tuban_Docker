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
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->json('pj_ids')->nullable()->after('icon');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status_new', ['open', 'onprogress', 'confirm WA', 'closed'])->default('open')->after('status');
            $table->enum('wa_status', ['Pending', 'Sent', 'None'])->default('None')->after('status_new');
            $table->text('wa_template')->nullable()->after('wa_status');
        });

        // Optional: Migrate old status to new status if needed, 
        // but since it's an overhaul, we might just drop the old column later.
        // For now, I'll keep both or just swap them.
        // Actually, let's just drop the old status and rename the new one if possible, 
        // or just use the new column names.
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['status_new', 'wa_status', 'wa_template']);
        });

        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropColumn('pj_ids');
        });
    }
};
