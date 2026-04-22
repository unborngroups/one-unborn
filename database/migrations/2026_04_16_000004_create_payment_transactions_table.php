<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_batch_id');
            $table->unsignedBigInteger('purchase_invoice_id');
            $table->string('razorpay_payment_id', 100);
            $table->string('razorpay_payout_id', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['created', 'processing', 'completed', 'failed', 'refunded', 'reversed'])->default('created');
            $table->text('razorpay_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            $table->foreign('payment_batch_id')->references('id')->on('payment_batches')->onDelete('cascade');
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('cascade');
            
            $table->index(['payment_batch_id', 'status']);
            $table->index(['razorpay_payment_id']);
            $table->unique(['razorpay_payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
