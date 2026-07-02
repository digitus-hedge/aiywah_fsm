<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // add the enum value first (raw, so it works on all Laravel versions)
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('Pending','Approved','Forwarded','Rejected','Assigned') NOT NULL DEFAULT 'Pending'");

        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_user_id')->nullable()->after('status');
            $table->unsignedBigInteger('service_domain_id')->nullable()->after('assigned_user_id');
            $table->timestamp('dispatched_at')->nullable()->after('service_domain_id');

            $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('service_domain_id')->references('id')->on('service_domains')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropForeign(['service_domain_id']);
            $table->dropColumn(['assigned_user_id', 'service_domain_id', 'dispatched_at']);
        });

        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('Pending','Approved','Forwarded','Rejected') NOT NULL DEFAULT 'Pending'");
    }
};