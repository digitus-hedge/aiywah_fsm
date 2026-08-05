<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_service_category', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('service_category_id')
                  ->constrained('service_categories')
                  ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'service_category_id'], 'usc_user_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_service_category');
    }
};