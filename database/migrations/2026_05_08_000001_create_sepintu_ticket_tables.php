<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->string('reporter_name');
            $table->string('reporter_phone');
            $table->string('reporter_email')->nullable();
            $table->string('reporter_organization')->nullable();
            
            $table->foreignId('category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->string('attachment')->nullable();
            
            $table->enum('status', ['open', 'diproses', 'selesai', 'ditutup'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->string('unit_kerja')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('solution')->nullable();
            $table->timestamp('finished_at')->nullable();
            
            $table->boolean('pushed_to_kms')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users'); // if internal reporter
            $table->timestamps();
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('type'); // status_change, assigned_to, etc.
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_activities');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_categories');
    }
};
