<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::table('service_requests', function (Blueprint $table) {
            $table->timestamp('eta_at')->nullable()->after('dispatched_at');
            $table->timestamp('accepted_at')->nullable()->after('eta_at');
            $table->text('hold_reason')->nullable()->after('accepted_at');
            $table->timestamp('held_at')->nullable()->after('hold_reason');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'eta_at',
                'accepted_at',
                'hold_reason',
                'held_at',
            ]);
        });
    }
};