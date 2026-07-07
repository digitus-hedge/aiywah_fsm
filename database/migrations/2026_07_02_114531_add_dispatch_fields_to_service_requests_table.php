<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend the status ENUM so dispatch, punch-out and QC decisions
        //    can all be stored. Existing rows are untouched.
        //    - 'Assigned'            → set on dispatch
        //    - 'in_progress'         → set on punch-in  (WorkerPunchController)
        //    - 'qc_review'           → set on punch-out (WorkerPunchController)
        //    - 'Rework'              → QC fail
        //    - 'Completed'           → QC pass, in-warranty
        //    - 'Pending Invoice'     → QC pass, out-of-warranty
        DB::statement("
            ALTER TABLE service_requests
            MODIFY COLUMN status
            ENUM(
                'Pending','Approved','Forwarded','Rejected','Assigned',
                'in_progress','qc_review','Rework','Completed','Pending Invoice'
            )
            NOT NULL DEFAULT 'Pending'
        ");

        // 2. Dispatch columns (from the original migration).
        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_user_id')->nullable()->after('status');
            $table->unsignedBigInteger('service_domain_id')->nullable()->after('assigned_user_id');
            $table->timestamp('dispatched_at')->nullable()->after('service_domain_id');

            $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('service_domain_id')->references('id')->on('service_domains')->nullOnDelete();
        });

        // 3. QC review tracking columns.
        Schema::table('service_requests', function (Blueprint $table) {
            $table->timestamp('qc_reviewed_at')->nullable()->after('dispatched_at');
            $table->unsignedBigInteger('qc_reviewed_by')->nullable()->after('qc_reviewed_at');
            $table->text('rework_notes')->nullable()->after('qc_reviewed_by');
            $table->enum('warranty_scope', ['iw', 'oow'])->nullable()->after('rework_notes');

            $table->foreign('qc_reviewed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Drop QC columns.
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['qc_reviewed_by']);
            $table->dropColumn(['qc_reviewed_at', 'qc_reviewed_by', 'rework_notes', 'warranty_scope']);
        });

        // Drop dispatch columns.
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropForeign(['service_domain_id']);
            $table->dropColumn(['assigned_user_id', 'service_domain_id', 'dispatched_at']);
        });

        // Revert the ENUM to the original allowed set.
        // NOTE: any rows still holding a new status value (Assigned, qc_review,
        // Completed, etc.) will be blanked/truncated by MySQL on this narrowing.
        // Only roll back if no SR is in those states.
        DB::statement("
            ALTER TABLE service_requests
            MODIFY COLUMN status
            ENUM('Pending','Approved','Forwarded','Rejected')
            NOT NULL DEFAULT 'Pending'
        ");
    }
};