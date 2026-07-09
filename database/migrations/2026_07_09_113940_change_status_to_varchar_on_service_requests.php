<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_requests MODIFY status VARCHAR(30) NOT NULL DEFAULT 'Pending'");
        DB::statement("CREATE INDEX service_requests_status_index ON service_requests (status)");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX service_requests_status_index ON service_requests");
        DB::statement("ALTER TABLE service_requests MODIFY status TEXT NOT NULL");
    }
};