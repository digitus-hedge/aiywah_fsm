<?php
// database/migrations/xxxx_create_punch_photos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('punch_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punch_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['before', 'after']);
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['punch_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punch_photos');
    }
};