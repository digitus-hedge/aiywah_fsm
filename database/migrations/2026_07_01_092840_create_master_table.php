<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SERVICE CATEGORIES
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique();
            $table->string('description', 500)->nullable();
            $table->string('color_code', 20)->nullable();
            $table->string('icon', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. SERVICE DOMAINS
        Schema::create('service_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')
                  ->constrained('service_categories')
                  ->cascadeOnDelete();
            $table->string('domain_name');
            $table->string('description', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. EXPENSE CATEGORIES
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. PRIORITIES
        Schema::create('priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedInteger('display_order')->default(1);
            $table->string('color', 20)->default('#3b82f6');
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. SLA MATRIX
        Schema::create('sla_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('priority_id')
                  ->constrained('priorities')
                  ->cascadeOnDelete();
            $table->unsignedInteger('response_time');     // minutes
            $table->unsignedInteger('assignment_time');   // minutes
            $table->unsignedInteger('resolution_time');   // minutes
            $table->decimal('alert_percentage', 5, 2)->default(80);
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. WHATSAPP TEMPLATES
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('trigger_event')->nullable();
            $table->string('description', 500)->nullable();
            $table->text('body');
            $table->json('variables')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. ACTIVITY LOGS
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('module')->nullable();
            $table->string('activity_type')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('sla_matrix');
        Schema::dropIfExists('priorities');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('service_domains');
        Schema::dropIfExists('service_categories');
    }
};
