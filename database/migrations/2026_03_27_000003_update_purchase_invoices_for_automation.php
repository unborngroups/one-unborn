<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // Add new fields
            $table->string('vendor_name_raw')->after('company_id');
            $table->string('gstin')->nullable()->after('vendor_name_raw');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('gstin');
            $table->string('invoice_number')->after('vendor_id');
            $table->date('invoice_date')->nullable()->after('invoice_number');
            $table->decimal('amount', 15, 2)->nullable()->after('invoice_date');
            $table->decimal('tax_amount', 15, 2)->nullable()->after('amount');
            $table->decimal('total_amount', 15, 2)->nullable()->after('tax_amount');
            $table->text('raw_json')->nullable()->after('total_amount');
            $table->enum('status', ['draft','needs_review','verified','approved','paid','duplicate'])->default('draft')->change();
            $table->float('confidence_score')->nullable()->after('status');
            $table->unsignedBigInteger('created_by')->nullable()->after('confidence_score');

            // Remove/rename old fields
            $table->dropColumn(['invoice_no','vendor_name','vendor_gstin','sub_total','cgst_total','sgst_total','grand_total']);
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_name_raw','gstin','vendor_id','invoice_number','invoice_date','amount','tax_amount','total_amount','raw_json','confidence_score','created_by'
            ]);
            $table->string('invoice_no')->unique();
            $table->string('vendor_name');
            $table->string('vendor_gstin')->nullable();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('cgst_total', 15, 2)->default(0);
            $table->decimal('sgst_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('status', ['draft','sent','paid','cancelled'])->default('draft')->change();
        });
    }
};
