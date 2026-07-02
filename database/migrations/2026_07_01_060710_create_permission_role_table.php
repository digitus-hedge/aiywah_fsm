<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
         Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
 
            // Access level for this role on this permission:
            //   yes = full access, no = none, rls = filtered / row-level (partial)
            $table->enum('access', ['yes', 'no', 'rls'])->default('no');
 
            // Whether an Admin can optionally extend this permission to the role
            // (used by the Front Desk "Extend (Admin grant)" checkboxes).
            $table->boolean('can_grant')->default(false);
 
            $table->timestamps();
 
            // One row per role+permission pair.
            $table->unique(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
