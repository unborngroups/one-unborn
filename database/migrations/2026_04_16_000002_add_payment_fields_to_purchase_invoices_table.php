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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('purchase_invoices', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'partial'])->default('pending')->after('gst_number');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'razorpay_payment_id')) {
                $table->string('razorpay_payment_id', 100)->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'payment_batch_id')) {
                $table->unsignedBigInteger('payment_batch_id')->nullable()->after('razorpay_payment_id');
                $table->foreign('payment_batch_id')->references('id')->on('payment_batches')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'auto_payment_enabled')) {
                $table->boolean('auto_payment_enabled')->default(false)->after('payment_batch_id');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('auto_payment_enabled');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'payment_processed_at')) {
                $table->timestamp('payment_processed_at')->nullable()->after('paid_amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'payment_failure_reason')) {
                $table->text('payment_failure_reason')->nullable()->after('payment_processed_at');
            }
            
            // Add indexes for better performance
            if (!Schema::hasIndex('purchase_invoices', ['payment_status'])) {
                $table->index(['payment_status']);
            }
            if (!Schema::hasIndex('purchase_invoices', ['auto_payment_enabled'])) {
                $table->index(['auto_payment_enabled']);
            }
            if (!Schema::hasIndex('purchase_invoices', ['due_date', 'payment_status'])) {
                $table->index(['due_date', 'payment_status']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('purchase_invoices', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'razorpay_payment_id')) {
                $table->dropColumn('razorpay_payment_id');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'payment_batch_id')) {
                $table->dropForeign(['payment_batch_id']);
                $table->dropColumn('payment_batch_id');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'auto_payment_enabled')) {
                $table->dropColumn('auto_payment_enabled');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'payment_processed_at')) {
                $table->dropColumn('payment_processed_at');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'payment_failure_reason')) {
                $table->dropColumn('payment_failure_reason');
            }
            
            // Drop indexes if they exist
            if (Schema::hasIndex('purchase_invoices', ['payment_status'])) {
                $table->dropIndex(['payment_status']);
            }
            if (Schema::hasIndex('purchase_invoices', ['auto_payment_enabled'])) {
                $table->dropIndex(['auto_payment_enabled']);
            }
            if (Schema::hasIndex('purchase_invoices', ['due_date', 'payment_status'])) {
                $table->dropIndex(['due_date', 'payment_status']);
            }
        });
    }
};
