<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `service_requests`
            MODIFY `status` ENUM(
                'Pending',
                'Approved',
                'Forwarded',
                'Additional',
                'Rejected',
                'Assigned',
                'Quoted',
                'Quote Approved',
                'In Progress',
                'Quote Rejected',
                'Qc Review',
                'Rework',
                'Reschedule',
                'Accepted',
                'Pending Invoice',
                'Invoice Submitted',
                'Completed',
                'On Hold'
            ) NOT NULL DEFAULT 'Pending'
        ");
    }

    public function down(): void
    {
        DB::table('service_requests')
            ->where('status', 'Quote Approved')
            ->update(['status' => 'Quoted']);

        DB::statement("
            ALTER TABLE `service_requests`
            MODIFY `status` ENUM(
                'Pending',
                'Approved',
                'Forwarded',
                'Additional',
                'Rejected',
                'Assigned',
                'Quoted',
                'In Progress',
                'Quote Rejected',
                'Qc Review',
                'Rework',
                'Reschedule',
                'Accepted',
                'Pending Invoice',
                'Invoice Submitted',
                'Completed',
                'On Hold'
            ) NOT NULL DEFAULT 'Pending'
        ");
    }
};