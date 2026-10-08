<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_request_id')
                ->constrained('service_requests')
                ->cascadeOnDelete();

            // The quotation this invoice was raised from (null for SRs that never had one).
            $table->foreignId('quotation_id')
                ->nullable()
                ->constrained('quotations')
                ->nullOnDelete();

            // Filled in right after insert, from the row id: INV-2026-00012
            $table->string('invoice_no', 40)->nullable()->unique();

            /* Invoice summary */
            $table->date('invoice_date');
            $table->string('payment_terms', 30)->default('net_30');   // net_30 | custom
            $table->date('due_date');

            /* Line items: [{ name, description, qty, unit, price, amount }, ...] */
            $table->json('items');

            /* Totals - always calculated on the server */
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('additional_amount', 12, 2)->default(0);
            $table->string('additional_note')->nullable();
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('currency', 8)->default('INR');

            $table->text('notes')->nullable();
            $table->string('pdf_path')->nullable();

            // The user who created the invoice (shown as "Created By").
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};