<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_request_reschedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained();
            $t->timestamp('previous_eta_at')->nullable();
            $t->timestamp('new_eta_at')->nullable();
            $t->text('reason');
            $t->string('from_status', 40)->nullable();
            $t->timestamps();

            $t->index(['service_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_reschedules');
    }
};