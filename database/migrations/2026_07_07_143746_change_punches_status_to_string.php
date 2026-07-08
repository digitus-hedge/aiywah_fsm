<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen punches.status from ENUM to VARCHAR so it accepts the full
     * lifecycle: draft, punched_in, submitted, qc_review, qc_passed, rework.
     * ENUM was rejecting qc_passed / rework with "Data truncated for column 'status'".
     */
    public function up(): void
    {
        // Change the column type. Using raw SQL because Doctrine can't always
        // introspect ENUM columns, and this avoids needing doctrine/dbal.
        DB::statement("ALTER TABLE `punches` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'draft'");
    }

    /**
     * Revert to the original ENUM. Adjust the value list if your original
     * ENUM differed. Any rows holding a value not in this list would block
     * the down migration, so we normalise them first.
     */
    public function down(): void
    {
        // Normalise any values outside the original ENUM set before reverting.
        DB::table('punches')
            ->whereNotIn('status', ['draft', 'punched_in', 'submitted'])
            ->update(['status' => 'submitted']);

        DB::statement(
            "ALTER TABLE `punches` MODIFY `status` ENUM('draft','punched_in','submitted') NOT NULL DEFAULT 'draft'"
        );
    }
};