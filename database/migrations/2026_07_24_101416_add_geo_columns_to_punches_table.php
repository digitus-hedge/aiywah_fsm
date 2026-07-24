<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('punches', function (Blueprint $table) {
            $table->decimal('punch_in_lat', 10, 7)->nullable()->after('punch_in_at');
            $table->decimal('punch_in_lng', 10, 7)->nullable()->after('punch_in_lat');
            $table->decimal('punch_in_accuracy', 8, 2)->nullable()->after('punch_in_lng');
            $table->string('punch_in_address', 500)->nullable()->after('punch_in_accuracy');

            $table->decimal('punch_out_lat', 10, 7)->nullable()->after('punch_out_at');
            $table->decimal('punch_out_lng', 10, 7)->nullable()->after('punch_out_lat');
            $table->decimal('punch_out_accuracy', 8, 2)->nullable()->after('punch_out_lng');
            $table->string('punch_out_address', 500)->nullable()->after('punch_out_accuracy');

            $table->decimal('signature_lat', 10, 7)->nullable()->after('customer_signature_path');
            $table->decimal('signature_lng', 10, 7)->nullable()->after('signature_lat');
            $table->decimal('signature_accuracy', 8, 2)->nullable()->after('signature_lng');
            $table->string('signature_address', 500)->nullable()->after('signature_accuracy');
            $table->timestamp('signed_at')->nullable()->after('signature_address');
        });
    }

    public function down(): void
    {
        Schema::table('punches', function (Blueprint $table) {
            $table->dropColumn([
                'punch_in_lat', 'punch_in_lng', 'punch_in_accuracy', 'punch_in_address',
                'punch_out_lat', 'punch_out_lng', 'punch_out_accuracy', 'punch_out_address',
                'signature_lat', 'signature_lng', 'signature_accuracy', 'signature_address',
                'signed_at',
            ]);
        });
    }
};