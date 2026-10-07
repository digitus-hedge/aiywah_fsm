<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->nullable()->index();
            $table->foreignId('client_id')->nullable()->index();
            $table->string('sr_reference', 50)->nullable()->index();
            $table->string('recipient');                 // email address
            $table->string('client_name')->nullable();
            $table->string('event', 80);                 // 'Client Welcome', 'Project Added', etc.
            $table->string('status', 20)->default('pending');  // pending | sent | failed
            $table->string('mailable', 150);             // 'ClientWelcomeMail', 'ProjectAddedMail'
            $table->string('subject')->nullable();
            $table->text('message')->nullable();         // preview / body summary
            $table->text('error')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};