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
        Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('office_type')->nullable();
    $table->string('short_name')->nullable();
    $table->string('client_code')->nullable(); // auto-generate
    $table->bigInteger('head_office_id')->nullable();
    $table->string('client_name');
    $table->string('user_name')->nullable();                   // User Name

    $table->string('pan_number', 10)->nullable();
    $table->string('business_display_name')->nullable();

    // Address
    $table->string('address1')->nullable();
    $table->string('address2')->nullable();
    $table->string('address3')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->nullable();
    $table->string('pincode', 10)->nullable();
    
    // Business Contact
    $table->string('billing_spoc_name')->nullable();
    $table->string('billing_spoc_contact', 20)->nullable();
    $table->string('billing_spoc_email')->nullable();
    $table->string('billing_sequence')->nullable();
    $table->string('gstin', 20)->nullable();
    $table->string('invoice_email')->nullable();
    $table->string('invoice_cc', 1000)->nullable();
    $table->string('delivered_email')->nullable();
    $table->string('delivered_cc', 1000)->nullable();

    // Technical Support
    $table->string('support_spoc_name')->nullable();
    $table->string('support_spoc_mobile', 20)->nullable();
    $table->string('support_spoc_email')->nullable();
    // portal access
     $table->string('portal_username')->unique()->nullable();
            $table->string('portal_password')->nullable();
            $table->boolean('portal_active')->default(0);
            $table->timestamp('portal_last_login')->nullable();

    $table->enum('status', ['Active', 'Inactive'])->default('Active');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('clients');
    }
};
