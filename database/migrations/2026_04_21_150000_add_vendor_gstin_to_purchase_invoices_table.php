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
            // Add vendor_gstin column if it doesn't exist
            if (!Schema::hasColumn('purchase_invoices', 'vendor_gstin')) {
                $table->string('vendor_gstin')->nullable()->after('gstin');
            }
            
            // Add index for better performance
            if (Schema::hasColumn('purchase_invoices', 'vendor_gstin') && !Schema::hasIndex('purchase_invoices', 'idx_purchase_invoices_vendor_gstin')) {
                $table->index(['vendor_gstin'], 'idx_purchase_invoices_vendor_gstin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'vendor_gstin')) {
                $table->dropIndex(['idx_purchase_invoices_vendor_gstin']);
                $table->dropColumn('vendor_gstin');
            }
        });
    }
};
