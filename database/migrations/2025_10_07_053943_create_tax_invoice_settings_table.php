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
        Schema::create('tax_invoice_settings', function (Blueprint $table) {
            $table->id();
             $table->string('invoice_prefix')->default('INV');
        $table->integer('invoice_start_no')->default(1);
        $table->string('currency_symbol')->default('₹');
        $table->string('currency_code')->default('INR');
        $table->decimal('tax_percentage', 5, 2)->nullable(); // e.g., 18.00
        $table->string('billing_terms')->nullable(); // e.g., "Net 30 days"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_invoice_settings');
    }
};
