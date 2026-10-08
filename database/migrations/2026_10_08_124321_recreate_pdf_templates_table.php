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
    Schema::dropIfExists('pdf_templates');   // removes the earlier text-only version if it exists

    Schema::create('pdf_templates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();
        $table->string('header_image')->nullable();
        $table->string('letterhead_image')->nullable();
        $table->string('footer_image')->nullable();
        $table->boolean('status')->default(1);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('pdf_templates');
}
};
