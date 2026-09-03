<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-user state of every alert checkbox. Row is_enabled = true means
     * "include this section for this user in tomorrow's WhatsApp summary".
     * Seeded to true (all checked) for every user; Super Admin unchecks
     * from Master Settings -> Summary Alert -> Select Role -> Select User.
     */
    public function up(): void
    {
        Schema::create('user_alert_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alert_type_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'alert_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_alert_permissions');
    }
};