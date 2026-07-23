<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    private array $statuses = [
        'Pending',
        'Approved',
        'Forwarded',
        'Additional',          // ADDED
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

    private array $previous = [
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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('status', $this->statuses)->default('Pending')->change();
        });
    }

    public function down(): void
    {
        // Any row sitting on the removed value would be blanked by the conversion
        DB::table('service_requests')
            ->where('status', 'Additional')
            ->update(['status' => 'Forwarded']);

        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('status', $this->previous)->default('Pending')->change();
        });
    }
};