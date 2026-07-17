<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $statuses = [
        'Pending',
        'Approved',
        'Forwarded',
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
        'On Hold',
    ];

    public function up(): void
    {
        // Rows holding values outside the new set would be blanked by the conversion
        DB::table('service_requests')
            ->whereNull('status')
            ->orWhereNotIn('status', $this->statuses)
            ->update(['status' => 'Pending']);

        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('status', $this->statuses)->default('Pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('status', 30)->default('Pending')->change();
        });
    }
};
