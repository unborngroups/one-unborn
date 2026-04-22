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
        Schema::create('prefix_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 50); // PO, Feasibility, Invoice, etc.
            $table->string('prefix_base', 20); // FY (Financial Year), VB (Vendor Base), CB (Client Base), GN (General)
            $table->string('prefix_format', 100); // Pattern: PO/{FY}/{SEQUENCE}, FR/{CLIENT_CODE}/{SEQUENCE}
            $table->string('sequence_format', 20)->default('####'); // Number format: ####, #####, etc.
            $table->integer('sequence_length')->default(4); // Length of sequence number
            $table->integer('current_sequence')->default(0); // Current sequence number
            $table->boolean('reset_yearly')->default(true); // Reset sequence each financial year
            $table->boolean('reset_monthly')->default(false); // Reset sequence each month
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['document_type', 'prefix_base']);
            $table->unique(['document_type', 'prefix_base']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefix_configurations');
    }
};
