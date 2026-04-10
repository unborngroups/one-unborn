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
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'sales_invoice_mail_from_name')) {
                $table->string('sales_invoice_mail_from_name')->nullable()->after('invoice_mail_read_days');
            }

            if (!Schema::hasColumn('company_settings', 'sales_invoice_mail_from_address')) {
                $table->string('sales_invoice_mail_from_address')->nullable()->after('sales_invoice_mail_from_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'sales_invoice_mail_from_address')) {
                $table->dropColumn('sales_invoice_mail_from_address');
            }

            if (Schema::hasColumn('company_settings', 'sales_invoice_mail_from_name')) {
                $table->dropColumn('sales_invoice_mail_from_name');
            }
        });
    }
};
