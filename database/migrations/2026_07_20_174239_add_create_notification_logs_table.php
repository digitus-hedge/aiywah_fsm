<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('service_request_id')->nullable()->constrained()->nullOnDelete();
            $t->string('event', 40);              // sr_created | status_updated
            $t->string('title');
            $t->text('message');
            $t->string('from_status', 40)->nullable();
            $t->string('to_status', 40)->nullable();
            $t->foreignId('caused_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();

            $t->index(['read_at', 'created_at']);
            $t->index('service_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};