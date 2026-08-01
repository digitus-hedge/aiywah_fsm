<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Make alert_percentage optional BEFORE we stop writing to it,
        //    otherwise the next insert fails on a NOT NULL column.
        Schema::table('sla_matrix', function (Blueprint $table) {
            $table->decimal('alert_percentage', 5, 2)->nullable()->change();
        });

        // 2. Convert existing minute values to hours (round up, floor of 1).
        DB::table('sla_matrix')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('sla_matrix')->where('id', $row->id)->update([
                    'response_time'   => max(1, (int) ceil($row->response_time   / 60)),
                    'assignment_time' => max(1, (int) ceil($row->assignment_time / 60)),
                    'resolution_time' => max(1, (int) ceil($row->resolution_time / 60)),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Reverse order: hours back to minutes first...
        DB::table('sla_matrix')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('sla_matrix')->where('id', $row->id)->update([
                    'response_time'   => $row->response_time   * 60,
                    'assignment_time' => $row->assignment_time * 60,
                    'resolution_time' => $row->resolution_time * 60,
                ]);
            }
        });

        // ...then restore the NOT NULL constraint.
        DB::table('sla_matrix')->whereNull('alert_percentage')->update(['alert_percentage' => 80]);

        Schema::table('sla_matrix', function (Blueprint $table) {
            $table->decimal('alert_percentage', 5, 2)->nullable(false)->change();
        });
    }
};