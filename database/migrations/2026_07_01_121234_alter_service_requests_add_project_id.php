<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Force drop the column out here ONLY if it still exists in the database
        if (Schema::hasColumn('service_requests', 'project_site')) {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->dropColumn('project_site');
            });
        }

        // 2. Now run your fresh additions safely
        Schema::table('service_requests', function (Blueprint $table) {
            // Check to avoid duplicate column crashes if running again
            if (!Schema::hasColumn('service_requests', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('client_id');
                
                $table->foreign('project_id')
                      ->references('id')
                      ->on('projects')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('service_requests', 'project_site')) {
                $table->string('project_site', 191)->nullable()->after('project_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
        });
    }
};