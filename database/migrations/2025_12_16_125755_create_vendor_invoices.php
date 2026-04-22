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
        Schema::create('vendor_invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained();
    $table->string('invoice_no');
    $table->date('invoice_date');
    $table->decimal('subtotal', 12, 2);
    $table->decimal('gst_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2);
    $table->enum('status', ['Pending','Paid','Cancelled'])->default('Pending');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_invoices');
    }
};
