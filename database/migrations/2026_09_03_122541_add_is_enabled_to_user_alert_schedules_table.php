<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_alert_schedules', function (Blueprint $table) {
            // Defaults FALSE on purpose - a brand-new user must be explicitly
            // enabled by a Super Admin before their first summary goes out.
            $table->boolean('is_enabled')->default(false)->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_alert_schedules', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
    }
};