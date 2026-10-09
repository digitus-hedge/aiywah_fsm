<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            $table->string('template_name', 150)->nullable()->after('id');
        });

        // Name the existing templates after their company so none is blank
        foreach (DB::table('pdf_templates')->get() as $t) {
            $name = DB::table('clients')->where('id', $t->client_id)->value('company_name');
            DB::table('pdf_templates')->where('id', $t->id)
                ->update(['template_name' => $name ?: 'Template ' . $t->id]);
        }

        // Company is no longer required
        DB::statement('ALTER TABLE pdf_templates MODIFY client_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            $table->dropColumn('template_name');
        });
    }
};