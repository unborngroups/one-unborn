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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

             // Company (for multi-company support)
            $table->unsignedBigInteger('company_id')->nullable();

            // Invoice basic info
            $table->string('invoice_no')->unique();   // INV-0001
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            // Customer info
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_gstin')->nullable();

            // Amounts
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('cgst_total', 15, 2)->default(0);
            $table->decimal('sgst_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            // Status
            $table->enum('status', [
                'draft',
                'sent',
                'paid',
                'cancelled'
            ])->default('draft');

            // Notes
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
