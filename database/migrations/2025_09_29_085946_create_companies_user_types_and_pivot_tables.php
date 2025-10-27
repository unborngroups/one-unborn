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
         // 1️⃣ Companies Table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
             // 🏷️ Basic Details
            $table->string('trade_name')->nullable();              // Trade/Brand Name
            $table->string('company_name');                        // Company Name
            $table->string('cin_llpin')->nullable();               // Business Number (CIN / LLPIN)

            // ☎️ Contact Details
            $table->string('company_phone')->nullable();           // Company Phone (landline)
            $table->string('alternative_contact')->nullable();     // Alternative contact number
            $table->string('email_1')->nullable();                 // Company Email
            $table->string('email_2')->nullable();                 // Secondary email (optional)
            $table->string('website')->nullable();                 // Company Website

            // 🏢 Address & Registration
            $table->string('gst_no')->nullable();                  // GSTIN (fetched via API)
            $table->string('pan_number')->nullable();              // PAN Number
            $table->text('address')->nullable();                   // Address (fetched or typed manually)

            // 📍 Branch & Social Media
            $table->string('branch_locations')->nullable();        // Store Location URL / Google Place ID
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();

            // 🏦 Bank Details
            $table->string('acc_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('bank_name')->nullable();

            // 💳 UPI Details
            $table->string('upi_id')->nullable();
            $table->string('upi_number')->nullable();
            $table->decimal('opening_balance', 15, 2)->nullable();

            // 🧾 Branding (Logos / Signatures)
            $table->string('billing_logo')->nullable();
            $table->string('billing_sign_normal')->nullable();
            $table->string('billing_sign_digital')->nullable();

            // 🎨 Theme
            $table->string('color')->default('#333333');
            $table->string('logo')->nullable();

            // ⚙️ Status
       
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // 2️⃣ User Types Table (renamed to user_types ✅)
        // Schema::create('user_types', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('description');
        //     $table->enum('status', ['Active', 'Inactive'])->default('Active');
        //     $table->timestamps();
        // });

        // 3️⃣ Pivot Table: company_user (Many-to-Many)
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user');
        // Schema::dropIfExists('user_types');
        Schema::dropIfExists('companies');
    }
};
