<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('performance_score')->nullable()->after('status');
            $table->text('evaluation_comment')->nullable()->after('performance_score');
            $table->timestamp('feedback_submitted_at')->nullable()->after('evaluation_comment');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['performance_score', 'evaluation_comment', 'feedback_submitted_at']);
        });
    }
};