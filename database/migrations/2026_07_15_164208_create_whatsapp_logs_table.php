<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sr_reference')->nullable()->index();
            $table->string('recipient')->index();          // phone number
            $table->string('client_name')->nullable();
            $table->string('event')->index();              // trigger event
            $table->string('status')->default('Pending')->index(); // Delivered|Sent|Failed|Pending
            $table->string('template')->nullable();
            $table->text('message')->nullable();           // preview / rendered body
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->string('wamid')->nullable()->index();  // WhatsApp message id
            $table->text('error')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};