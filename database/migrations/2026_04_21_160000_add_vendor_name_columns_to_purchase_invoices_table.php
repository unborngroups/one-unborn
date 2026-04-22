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
            // Add missing vendor name columns if they don't exist
            if (!Schema::hasColumn('purchase_invoices', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('vendor_id');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'vendor_name_raw')) {
                $table->string('vendor_name_raw')->nullable()->after('vendor_name');
            }
            
            // Add other missing columns that might be needed
            if (!Schema::hasColumn('purchase_invoices', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'sub_total')) {
                $table->decimal('sub_total', 15, 2)->nullable()->after('amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'cgst_total')) {
                $table->decimal('cgst_total', 15, 2)->nullable()->after('tax_amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'sgst_total')) {
                $table->decimal('sgst_total', 15, 2)->nullable()->after('cgst_total');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'arc_amount')) {
                $table->decimal('arc_amount', 15, 2)->nullable()->after('sgst_total');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'otc_amount')) {
                $table->decimal('otc_amount', 15, 2)->nullable()->after('arc_amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'static_amount')) {
                $table->decimal('static_amount', 15, 2)->nullable()->after('otc_amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'confidence_score')) {
                $table->decimal('confidence_score', 5, 2)->nullable()->after('static_amount');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'po_invoice_file')) {
                $table->string('po_invoice_file')->nullable()->after('confidence_score');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'vendor_email')) {
                $table->string('vendor_email')->nullable()->after('po_invoice_file');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'vendor_phone')) {
                $table->string('vendor_phone')->nullable()->after('vendor_email');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'vendor_address')) {
                $table->text('vendor_address')->nullable()->after('vendor_phone');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'type')) {
                $table->string('type')->default('purchase')->after('vendor_address');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            
            if (!Schema::hasColumn('purchase_invoices', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $columnsToDrop = [
                'vendor_name', 'vendor_name_raw', 'amount', 'sub_total', 'cgst_total', 'sgst_total',
                'arc_amount', 'otc_amount', 'static_amount', 'confidence_score', 'po_invoice_file',
                'vendor_email', 'vendor_phone', 'vendor_address', 'type', 'created_by', 'updated_by', 'deleted_by'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('purchase_invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
