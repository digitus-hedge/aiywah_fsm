<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('invoice_code')->nullable()->after('attachments');
            $table->decimal('invoice_total', 12, 2)->nullable()->after('invoice_code');
            $table->string('invoice_path')->nullable()->after('invoice_total');
            $table->timestamp('invoice_submitted_at')->nullable()->after('invoice_path');
            $table->foreignId('invoice_uploaded_by')->nullable()->after('invoice_submitted_at')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('hop_approved_at')->nullable()->after('invoice_uploaded_by');
            $table->foreignId('hop_approved_by')->nullable()->after('hop_approved_at')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_uploaded_by');
            $table->dropConstrainedForeignId('hop_approved_by');
            $table->dropColumn([
                'invoice_code',
                'invoice_total',
                'invoice_path',
                'invoice_submitted_at',
                'hop_approved_at',
            ]);
        });
    }
};