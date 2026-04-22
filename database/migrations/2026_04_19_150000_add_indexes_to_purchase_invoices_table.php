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
            // Add indexes for frequently queried columns only if columns and indexes don't exist
            if (Schema::hasColumn('purchase_invoices', 'status') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_status')) {
                $table->index(['status'], 'idx_purchase_invoices_status');
            }
            if (Schema::hasColumn('purchase_invoices', 'email_log_id') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_email_log_id')) {
                $table->index(['email_log_id'], 'idx_purchase_invoices_email_log_id');
            }
            if (Schema::hasColumn('purchase_invoices', 'created_at') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_created_at')) {
                $table->index(['created_at'], 'idx_purchase_invoices_created_at');
            }
            if (Schema::hasColumn('purchase_invoices', 'vendor_id') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_vendor_id')) {
                $table->index(['vendor_id'], 'idx_purchase_invoices_vendor_id');
            }
            if (Schema::hasColumn('purchase_invoices', 'invoice_no') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_invoice_no')) {
                $table->index(['invoice_no'], 'idx_purchase_invoices_invoice_no');
            }
            if (Schema::hasColumn('purchase_invoices', 'gstin') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_gstin')) {
                $table->index(['gstin'], 'idx_purchase_invoices_gstin');
            }
            
            // Composite index for common queries only if columns and indexes don't exist
            if (Schema::hasColumn('purchase_invoices', 'status') && Schema::hasColumn('purchase_invoices', 'created_at') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_status_created_at')) {
                $table->index(['status', 'created_at'], 'idx_purchase_invoices_status_created_at');
            }
            if (Schema::hasColumn('purchase_invoices', 'status') && Schema::hasColumn('purchase_invoices', 'email_log_id') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_status_email_log_id')) {
                $table->index(['status', 'email_log_id'], 'idx_purchase_invoices_status_email_log_id');
            }
        });

        Schema::table('email_logs', function (Blueprint $table) {
            // Add indexes for email logs only if columns and indexes don't exist
            if (Schema::hasColumn('email_logs', 'created_at') && !Schema::hasIndex('email_logs', 'idx_email_logs_created_at')) {
                $table->index(['created_at'], 'idx_email_logs_created_at');
            }
            if (Schema::hasColumn('email_logs', 'status') && !Schema::hasIndex('email_logs', 'idx_email_logs_status')) {
                $table->index(['status'], 'idx_email_logs_status');
            }
            if (Schema::hasColumn('email_logs', 'file_hash') && !Schema::hasIndex('email_logs', 'idx_email_logs_file_hash')) {
                $table->index(['file_hash'], 'idx_email_logs_file_hash');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropIndex('idx_purchase_invoices_status');
            $table->dropIndex('idx_purchase_invoices_email_log_id');
            $table->dropIndex('idx_purchase_invoices_created_at');
            $table->dropIndex('idx_purchase_invoices_vendor_id');
            $table->dropIndex('idx_purchase_invoices_invoice_no');
            $table->dropIndex('idx_purchase_invoices_gstin');
            $table->dropIndex('idx_purchase_invoices_status_created_at');
            $table->dropIndex('idx_purchase_invoices_status_email_log_id');
        });

        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropIndex('idx_email_logs_created_at');
            $table->dropIndex('idx_email_logs_status');
            $table->dropIndex('idx_email_logs_file_hash');
        });
    }
};
