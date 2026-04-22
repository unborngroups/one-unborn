<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->integer('payment_terms_days')->default(15)->after('ifsc_code');
            $table->string('razorpay_vendor_id', 100)->nullable()->after('payment_terms_days');
            $table->boolean('auto_payment_enabled')->default(false)->after('razorpay_vendor_id');
            $table->decimal('monthly_payment_limit', 15, 2)->default(500000.00)->after('auto_payment_enabled');
            $table->text('payment_notes')->nullable()->after('monthly_payment_limit');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms_days',
                'razorpay_vendor_id', 
                'auto_payment_enabled',
                'monthly_payment_limit',
                'payment_notes'
            ]);
        });
    }
};
