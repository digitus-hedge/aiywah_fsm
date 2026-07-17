<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_notify_to_client_mobiles_table.php
    public function up(): void
    {
        Schema::table('client_mobiles', function (Blueprint $table) {
            $table->boolean('notify')->default(0)->after('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('client_mobiles', function (Blueprint $table) {
            $table->dropColumn('notify');
        });
    }
};
