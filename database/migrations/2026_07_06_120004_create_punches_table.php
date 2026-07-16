<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('punches', function (Blueprint $table) {
            $table->id();

            // Your existing service_requests table
            $table->foreignId('service_request_id')
                  ->constrained('service_requests')
                  ->cascadeOnDelete();

            // Worker == User with role ML (Maintenance Lead)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Punch in
            $table->timestamp('punch_in_at')->nullable();
            $table->string('site_location')->nullable();   // entered at punch-in (not on SR)
            $table->text('work_description')->nullable();
            $table->string('start_photo_path')->nullable();
    
            // Punch out
            $table->timestamp('punch_out_at')->nullable();
            $table->string('finish_photo_path')->nullable();
            $table->text('completion_summary')->nullable();

            // Billing
            $table->decimal('materials_subtotal', 12, 2)->default(0);
            $table->decimal('labour_charge', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('notes')->nullable();

            // Sign-off
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            $table->enum('status', ['draft', 'punched_in', 'submitted'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punches');
    }
};