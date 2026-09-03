<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permission_role', function (Blueprint $table) {
            // Reads follow `access`; writes are additionally narrowed to rows
            // the user owns. Only meaningful when access = 'yes' - under 'rls'
            // the read scope already restricts them to their own rows.
            $table->boolean('write_own_only')
                ->default(false)
                ->after('is_readonly');
        });
    }

    public function down(): void
    {
        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropColumn('write_own_only');
        });
    }
};