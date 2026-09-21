<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('clients_unique_code_unique');
            $table->dropUnique('clients_primary_mobile_unique');

            // keep them indexed for fast lookups, just not unique
            $table->index('unique_code');
            $table->index('primary_mobile');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['unique_code']);
            $table->dropIndex(['primary_mobile']);

            $table->unique('unique_code');
            $table->unique('primary_mobile');
        });
    }
};