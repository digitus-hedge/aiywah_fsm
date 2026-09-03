<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Master list" of every Daily Summary alert heading, grouped by role.
     * This is the table the Permission Seeder fills. The Master Settings ->
     * Summary Alert screen reads from here to know which checkboxes to show
     * for a given role.
     */
    public function up(): void
    {
        Schema::create('alert_types', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30);                 // admin | hop | se | ml
            $table->string('key', 60)->unique();         // e.g. admin_logged, se_dispatch_avg_time
            $table->string('title', 150);                // heading shown in the checklist / WA message
            $table->text('description')->nullable();     // the bullet text explaining the metric
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default_checked')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_types');
    }
};