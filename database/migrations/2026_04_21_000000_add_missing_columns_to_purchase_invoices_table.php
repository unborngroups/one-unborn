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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('purchase_invoices', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('invoice_date');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('invoice_date');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'net_amount')) {
                $table->decimal('net_amount', 15, 2)->nullable()->after('grand_total');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'gst_number')) {
                $table->string('gst_number')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'gstin')) {
                $table->string('gstin')->nullable()->after('gst_number');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'email_log_id')) {
                $table->unsignedBigInteger('email_log_id')->nullable()->after('gstin');
                $table->foreign('email_log_id')->references('id')->on('email_logs')->onDelete('set null');
            }
            
            // Add indexes for better performance
            if (Schema::hasColumn('purchase_invoices', 'vendor_id') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_vendor_id')) {
                $table->index(['vendor_id'], 'idx_purchase_invoices_vendor_id');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'invoice_no') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_invoice_no')) {
                $table->index(['invoice_no'], 'idx_purchase_invoices_invoice_no');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'gstin') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_gstin')) {
                $table->index(['gstin'], 'idx_purchase_invoices_gstin');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'email_log_id') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_email_log_id')) {
                $table->index(['email_log_id'], 'idx_purchase_invoices_email_log_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('purchase_invoices', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
            
            if (Schema::hasColumn('purchase_invoices', 'email_log_id')) {
                $table->dropForeign(['email_log_id']);
                $table->dropColumn('email_log_id');
            }
            
            // Drop other columns
            $columnsToDrop = ['invoice_no', 'due_date', 'net_amount', 'gst_number', 'gstin'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('purchase_invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
