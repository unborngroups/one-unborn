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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code')->unique(); // auto-generate
            $table->string('vendor_name');
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
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_mobile', 20)->nullable();
            $table->string('contact_person_email')->nullable();

            // Vendor Business Details
            $table->string('gstin', 20)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('bank_account_no', 30)->nullable();
            $table->string('ifsc_code', 20)->nullable();

            $table->enum('status', ['Active', 'Inactive'])->default('Active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
