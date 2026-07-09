<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('erp_quote_ref')->nullable()->after('attachments');
            $table->string('quote_path')->nullable()->after('erp_quote_ref');
            $table->timestamp('quote_submitted_at')->nullable()->after('quote_path');
            $table->timestamp('client_approved_at')->nullable()->after('quote_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'erp_quote_ref',
                'quote_path',
                'quote_submitted_at',
                'client_approved_at',
            ]);
        });
    }
};