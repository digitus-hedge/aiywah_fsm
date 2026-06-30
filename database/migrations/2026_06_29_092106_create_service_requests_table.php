
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('project_site');
            $table->unsignedTinyInteger('service_type_id'); 

            $table->string('reported_by');
            $table->string('priority_level');

            $table->text('issue_description');
            $table->text('internal_remark')->nullable();
            $table->text('status');
            $table->json('attachments')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};