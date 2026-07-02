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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            // Short code used across the UI / permission matrix (SA, AD, HP, ML, FD, AC)
            $table->string('code', 4)->unique();

            // Display name and one-line description shown in the role picker
            $table->string('name');
            $table->text('tagline')->nullable();

            // Presentation metadata mirrored from the front-end ROLE_META object
            $table->string('icon')->nullable();      // bootstrap-icon class, e.g. bi-shield-fill-check
            $table->string('color', 9)->nullable();  // hex, e.g. #7A6140
            $table->string('bg')->nullable();        // rgba background token

            // Whether this role can receive extended (grantable) permissions — FD only, by default
            $table->boolean('is_grantable')->default(false);

            // Ordering for the dropdown / listings
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};