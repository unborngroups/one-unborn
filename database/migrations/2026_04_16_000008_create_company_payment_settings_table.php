<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Razorpay Configuration (Encrypted)
            $table->string('razorpay_key_id')->nullable();
            $table->text('razorpay_key_secret')->nullable(); // Encrypted field
            $table->string('razorpay_account_number')->nullable(); // Virtual account number
            $table->text('webhook_secret')->nullable(); // Encrypted field
            
            // Payment Batching Configuration
            $table->enum('payment_batching_mode', ['single', 'vendor_bulk'])->default('single');
            $table->boolean('cashflow_control_enabled')->default(false);
            $table->decimal('minimum_balance_threshold', 10, 2)->default(1000.00);
            $table->decimal('max_daily_payout_limit', 10, 2)->default(100000.00);
            
            // Multi-Tenant Isolation
            $table->boolean('isolation_enabled')->default(true);
            $table->enum('approval_workflow', ['single_level', 'two_level'])->default('two_level');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->unique(['company_id']);
            $table->index(['company_id', 'payment_batching_mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_payment_settings');
    }
};
