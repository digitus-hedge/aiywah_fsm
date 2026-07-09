<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('punch_items', function (Blueprint $table) {
            $table->string('category', 60)->nullable()->after('name');
            $table->string('recon_status', 20)->default('pending')->after('line_total');
            $table->foreignId('reconciled_by')->nullable()->after('recon_status')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('reconciled_at')->nullable()->after('reconciled_by');
            $table->index('recon_status');
        });
    }

    public function down(): void
    {
        Schema::table('punch_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reconciled_by');
            $table->dropIndex(['recon_status']);
            $table->dropColumn(['category', 'recon_status', 'reconciled_at']);
        });
    }
};