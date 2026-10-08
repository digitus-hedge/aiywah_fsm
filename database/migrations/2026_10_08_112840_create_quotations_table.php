<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests');
            $table->string('quote_ref', 60)->index();
            $table->date('quote_date');
            $table->date('expiry_date')->nullable();

            $table->string('summary', 500);
            $table->text('notes')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('discount_type', 10);              // percent | flat
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('adjustment', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);

            $table->string('pdf_path')->nullable();
            $table->text('sent_to')->nullable();
            $table->text('sent_cc')->nullable();
            $table->string('email_subject', 200)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
