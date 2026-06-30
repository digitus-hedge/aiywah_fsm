<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('unique_code')->unique();
            $table->string('contact_name')->nullable();
            $table->string('primary_country', 6);
            $table->string('primary_mobile', 15);
            $table->string('designation')->nullable();
            $table->timestamps();
        });

        Schema::create('client_mobiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('country', 6);
            $table->string('mobile');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_mobiles');
        Schema::dropIfExists('clients');
    }
};