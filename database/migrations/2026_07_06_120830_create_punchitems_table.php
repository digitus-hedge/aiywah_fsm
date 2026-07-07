<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('punch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('qty', 10, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punch_items');
    }
};