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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'needs_review', 'verified', 'approved', 'paid', 'duplicate', 'failed'])->default('draft');
            $table->text('notes')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('gstin')->nullable();
            $table->string('vendor_gstin')->nullable();
            $table->unsignedBigInteger('email_log_id')->nullable();
            $table->json('raw_json')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('vendor_name_raw')->nullable();
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone')->nullable();
            $table->text('vendor_address')->nullable();
            $table->string('type')->default('purchase');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('cgst_total', 15, 2)->default(0);
            $table->decimal('sgst_total', 15, 2)->default(0);
            $table->decimal('arc_amount', 15, 2)->default(0);
            $table->decimal('otc_amount', 15, 2)->default(0);
            $table->decimal('static_amount', 15, 2)->default(0);
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->string('po_invoice_file')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index(['vendor_id'], 'idx_purchase_invoices_vendor_id');
            $table->index(['invoice_no'], 'idx_purchase_invoices_invoice_no');
            $table->index(['status'], 'idx_purchase_invoices_status');
            $table->index(['email_log_id'], 'idx_purchase_invoices_email_log_id');
            $table->index(['created_at'], 'idx_purchase_invoices_created_at');

            // Add foreign keys
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('email_log_id')->references('id')->on('email_logs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
