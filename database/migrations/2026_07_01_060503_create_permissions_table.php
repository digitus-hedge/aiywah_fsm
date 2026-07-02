<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
 
            // Machine key used in code / route checks, e.g. "dashboard", "sr_registration"
            $table->string('key')->unique();
 
            // Human label shown in the permission matrix, e.g. "SR Registration"
            $table->string('name');
 
            // Sidebar grouping: Main / Workflow / Finance / System
            $table->string('section');
 
            // Feather / bootstrap icon for display
            $table->string('icon')->nullable();
 
            // Optional route name this page maps to (nullable for pages without a route yet)
            $table->string('route')->nullable();
 
            // Ordering within its section
            $table->unsignedSmallInteger('sort_order')->default(0);
 
            $table->timestamps();
        });
    }
   
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
